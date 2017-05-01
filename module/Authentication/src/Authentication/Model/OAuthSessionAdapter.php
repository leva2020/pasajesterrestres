<?php
namespace Authentication\Model;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Exception as HttpException;
use Zend\Stdlib\ArrayUtils;
use \Zend\Authentication\Result;
use Register\Model\UserTable;
use Register\Model\User;

/**
 * Adaptador para manejar la autenticación de usuarios utilizando el
 * Servidor Casper OAuth2
 */
class OAuthSessionAdapter implements AdapterInterface
{

    private $credentials;

    protected $clientConfig;

    protected $clientParams;

    protected $clientUri;

    protected $userTable;

    protected $oAuthAccessTokenTable;

    /**
     * Sets OAuth2 Code for authentication
     *
     * @return void
     */
    public function __construct(UserTable $userTable, OAuthAccessTokenTable $oAuthAccessTokenTable)
    {
        $this->userTable = $userTable;
        $this->oAuthAccessTokenTable = $oAuthAccessTokenTable;
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
            
            /*
             * Inicio Zend HttpClient
             */
            $client = new HttpClient();
            $client->setAdapter('Zend\Http\Client\Adapter\Curl');
            $client->setUri($this->clientUri);
            $client->setMethod('POST');
            $clientPostParams = $this->credentials + $this->clientParams;
            $client->setParameterPost($clientPostParams);

            $response = $client->send();

            if (! $response->isSuccess()) {
                // report failure
                $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
                $response->setContent($message);
                error_log("http client request fail");
                // @todo revisar si se requiere redirect a pagina de error
            }

            $grantResponse = json_decode($response->getBody());



            if (!isset($grantResponse->error)) {
                $token = $this->oAuthAccessTokenTable->get($grantResponse->access_token);
                $user = $this->userTable->get($token->getUserId());

                $grantResponseArray = array(
                    'access_token' => $token->getAccessToken(),
                    'expires_in' => $token->getExpires(),
                    'token_type' => 'default_login',
                    'scope' => 'login',
                    'refresh_token' => $token->getAccessToken()
                );

                $identityArray = array(
                    'id' => $user->getId(), 
                    'username' => $user->getUsername(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'email' => $user->getEmail(),
                    'status' => $user->getStatus(),
                    'user_type_id' => $user->getUserTypeId(),
                );

                $identity = json_decode(json_encode(ArrayUtils::merge($identityArray, $grantResponseArray)), false);

                $status = Result::SUCCESS;
            } else {
                
                $messages = array(
                    'error' => $grantResponse->error
                );
                
                $user = $this->userTable->getByUsernameOrEmail($this->credentials['username']);
                
                if ($user instanceof User) {
                    $status = Result::FAILURE_CREDENTIAL_INVALID;
                    $username = array(
                        'username' => $this->credentials['username']
                    );
                    $messages = array_merge($messages, $username);
                } else {
                    $status = Result::FAILURE_IDENTITY_NOT_FOUND;
                }
            }
        } catch (HttpException $e) {
            $status = Result::FAILURE_UNCATEGORIZED;
            error_log("Se ha producido un error al intentar autenticar: " . $e->getMessage());
        } catch (\Exception $e) {
            $status = Result::FAILURE_UNCATEGORIZED;
            error_log("Error al autenticar usuario: " . $e->getMessage());
        }
        
        return new Result($status, $identity, $messages);
    }

    public function setParams($credentials, $clientConfig, $clientParams)
    {
        $this->credentials = $credentials;
        $this->clientConfig = $clientConfig;
        $this->clientParams = $clientParams;

        if (isset($this->clientConfig['base_uri']) && isset($this->clientConfig['access_token_uri'])) {
            $this->clientUri = $this->clientConfig['base_uri'] . $this->clientConfig['access_token_uri'];
        }
    }
}
