<?php
namespace Authentication\Model;

use Zend\Authentication\Adapter\AdapterInterface;
use \Zend\Authentication\Result;
use Zend\Stdlib\ArrayUtils;
use Authentication\Model\OAuthAccessToken;
use Register\Model\User;
use Google_Client;
use Google_Service_Plus;
use Google_Auth_Exception;
use Google_Exception;
use Zend\Session\Container;

/**
 * Adaptador para manejar la autenticación de usuarios utilizando el
 * Servidor Casper OAuth2
 */
class GoogleSessionAdapter implements AdapterInterface
{

    private $config;

    private $code;

    private $token;

    private $clientId;

    protected $userTable;

    protected $oAuthAccessTokenTable;

    /**
     * Sets OAuth2 Code for authentication
     *
     * @return void
     */
    public function __construct($sm)
    {
        $config = $sm->get('config');
        $this->config = $config['oauth2_config'];
        $this->userTable = $sm->get('Register\Model\UserTable');
        $this->oAuthAccessTokenTable = $sm->get('Authentication\Model\OAuthAccessTokenTable');
    }

    /**
     * Performs an authentication attempt
     *
     * @return \\Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        $status = Result::FAILURE;
        $identity = null;
        $messages = array();
        
        try {
            $client = new Google_Client();
            $client->setApplicationName($this->config['google_login']['application_name']);
            $client->setClientId($this->config['google_login']['application_id']);
            $client->setClientSecret($this->config['google_login']['application_secret']);
            $client->setRedirectUri('postmessage');
            
            $plus = new Google_Service_Plus($client);
            
            // $plusResources = new Google_Service_Plus_People_Resource($client);
            $client->authenticate($this->code);
            
            $token = json_decode($client->getAccessToken());
            
            // You can read the Google user ID in the ID token.
            // "sub" represents the ID token subscriber which in our case
            // is the user ID. This sample does not use the user ID.
            $attributes = $client->verifyIdToken($token->id_token, $this->config['google_login']['application_id'])->getAttributes();
            
            $gplus_id = $attributes["payload"]["sub"];
            
            $client->setAccessToken($client->getAccessToken());
            $token = json_decode($client->getAccessToken());
            
            if (isset($token->access_token)) {
                $tokenExpireDate = time() + $this->config['login_grant_server_config']['access_lifetime'];
                $grantResponseArray = array(
                    'access_token' => $token->access_token,
                    'expires_in' => date('Y-m-d H:i:s', $tokenExpireDate),
                    'token_type' => 'google_login',
                    'scope' => 'login',
                    'refresh_token' => $token->access_token
                );
                
                // $identity = json_decode(json_encode($grantResponseArray), false);
                
                $profile = $plus->people->get($gplus_id);
                
                $email = $profile->getEmails();

                $emailJson = json_encode($email[0]);
                $nameJson = json_encode($profile->getName());
                // $people = $plus->people->listPeople('me', 'visible', array());
                $profileJson = json_encode($profile);
                
                $profileArray = json_decode($profileJson, true);
                $emailArray = array(
                    'email' => json_decode($emailJson, true)
                );
                $nameArray = json_decode($nameJson, true);
                
                $userInfo = array_merge($profileArray, $nameArray, $emailArray);
                
                $socialMetadata = json_encode($userInfo);
                
                $user = (null !== $userInfo['email']['value']) ? $this->userTable->getByEmail($userInfo['email']['value']) : false;
                
                if (! $user) {
                    $user = $this->userTable->getByUid($userInfo['id']);
                }
                                
                $newUser = 0;
                
                if (! $user) {
                    $user = new User();

                    if (! isset($userInfo['nickname'])) {
                        $user->setUsername($this->setUsername($userInfo));
                    } else {
                        $user->setUsername($this->checkUsername($userInfo));
                    }
                    
                    $user->setFirstName($userInfo['givenName']);
                    $user->setLastName($userInfo['familyName']);
                    $user->setEmail($userInfo['email']['value']);
                    $user->setDocument($userInfo['id']);
                    $user->setDocumentTypeId(1);
                    $user->setUid($userInfo['id'], false);
                    
                    $user->setPassword(sha1($userInfo['email']['value'] . time()));

                    // @internal se modifica estado a inactivo para requerimiento de redirección a documento y tipo de documento
                    
                    $googleStatus = $this->config['google_login']['default_status'];
                    $user->setStatus($googleStatus);
                    $user->setUserTypeId(2);
                    
                    $userId = $this->userTable->save($user, false);
                    $newUser = $this->userTable->get($userId);
                    $user->setId($userId);
                } else {
                    $userId = $user->getId();
                }
                
                $identityArray = array();

                if ($userId > 0) {
                    $databaseToken = new OAuthAccessToken();
                    $databaseToken->setAccessToken($grantResponseArray['access_token']);
                    $databaseToken->setClientId($this->clientId);
                    $databaseToken->setExpires($grantResponseArray['expires_in']);
                    $databaseToken->setScope($grantResponseArray['scope']);
                    $databaseToken->setUserId($user->getId());
                    
                    $savedToken = $this->oAuthAccessTokenTable->save($databaseToken);
                    
                    $identityArray = array(
                        'id' => $user->getId(), 
                        'username' => $user->getUsername(),
                        'first_name' => $user->getFirstName(),
                        'last_name' => $user->getLastName(),
                        'email' => $user->getEmail(),
                        'status' => $user->getStatus(),
                        'user_type_id' => $user->getUserTypeId(),
                    );
                }
                $identity = json_decode(json_encode(ArrayUtils::merge($identityArray, $grantResponseArray)), false);
            }
            
            if (null === $identity || isset($identity->error) || empty($identity)) {
                $status = Result::FAILURE_IDENTITY_NOT_FOUND;
                if (isset($identity->error) && isset($identity->error_description)) {
                    $messages = array(
                        $identity->error => $identity->error_description
                    );
                }
            } else {
                $status = Result::SUCCESS;
            }
        } catch (Google_Auth_Exception $ge) {
            $status = Result::FAILURE_UNCATEGORIZED;
            error_log("Google login Exception");
            error_log($ge->getMessage());
        } catch (\Exception $e) {
            $status = Result::FAILURE_UNCATEGORIZED;
            error_log("Google login Exception");
            error_log($e->getMessage());
        }
        
        return new Result($status, $identity, $messages);
    }

    public function setClientParams($code, $clientId)
    {
        $this->code = $code;
        $this->clientId = $clientId; 
    }

    public function setUsername($userdata, $username = false)
    {
        $nickname = (null != $userdata['nickname']) ? $userdata['nickname'] : $userdata['givenName'];
        $userString = ($username) ? $nickname : $userdata['givenName'];
        
        $tryUser = str_replace(' ', '', strtolower(substr($userString, 0, 16))) . rand(100000, 999999);
        
        try {
            $checkUser = $this->userTable->getByUserName($tryUser);
            if (! $checkUser) {
                return $tryUser;
            } else {
                return $this->setUsername($userdata);
            }
        } catch (\Exception $e) {
            error_log("error generando username: " . $e->getMessage());
            return false;
        }
    }

    public function checkUsername($userdata)
    {
        $checkUser = $this->userTable->getByUserName($userdata['nickname']);
        if (! $checkUser) {
            return $userdata['nickname'];
        } else {
            return $this->setUsername($userdata, true);
        }
    }
}
