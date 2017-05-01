<?php

/**
 * Zend Framework (http://framework.zend.com/).
 *
 * @link      http://github.com/zendframework/Authentication for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Escaper\Escaper;
use Application\Interfaces\ConfigAwareInterface;
use Application\Traits\ConfigAwareTrait;
use Authentication\Traits\OAuth2Trait;
use Authentication\Service\OAuth2ServiceInterface;
use Zend\Authentication\AuthenticationService;
use Authentication\Form\LoginForm;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\ArrayUtils;

class AuthServerController extends AbstractActionController implements ConfigAwareInterface
{
    use ConfigAwareTrait, OAuth2Trait;

    public function indexAction()
    {
        return array();
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        
        $auth->clearIdentity();
        $session = $auth->getIdentity();
        
        $auth->getStorage()->clear();

        return new ViewModel(array(
             'session' => $session
         ));
    }

    public function loginAction()
    {
        $escaper = new Escaper('utf-8');

        $type = $escaper->escapeHtmlAttr($this->params()
            ->fromRoute('type', ''));

        $client_id = $escaper->escapeHtmlAttr($this->params()
            ->fromRoute('client_id', 'pasajesterrestres'));
        $grant_type = $escaper->escapeHtmlAttr($this->params()
            ->fromRoute('grant_type', 'authorization_code'));
        $response_type = $escaper->escapeHtmlAttr($this->params()
            ->fromRoute('response_type', 'code'));
        $scope = $escaper->escapeHtmlAttr($this->params()
            ->fromRoute('scope', 'login'));
        $state = $escaper->escapeHtmlAttr((int) $this->params()
            ->fromRoute('state', rand(1000000000, getrandmax())));

        /**
         *
         * @internal La cabecera P3P se define para permitir la lectura de cookies en cross-domain para IE en todas sus versiones
         */
        $response = $this->getResponse();
        $response->getHeaders()->addHeaders(array(
            'P3P' => "CP='CAO PSA OUR'",
        ));

        $facebookAppId = $this->config['facebook_login']['application_id'];
        $googleAppId = $this->config['google_login']['application_id'];

        $auth = new AuthenticationService();
        $session = $auth->getIdentity();

        $form = new LoginForm();
        $form->get('client_id')->setValue($client_id);
        $form->get('grant_type')->setValue($grant_type);
        $form->get('response_type')->setValue($response_type);
        $form->get('scope')->setValue($scope);
        $form->get('state')->setValue($state);
        $form->get('submit')->setValue('Ingresar');

        $viewModel = new ViewModel();

        if ($type == 'lightbox') {
            // $viewModel->setTerminal(true);
            $this->layout("layout/lightbox-login-layout");
        }
        
        $viewModel->setVariables(array(
            'facebookAppId' => $facebookAppId,
            'googleAppId' => $googleAppId,
            'client_id' => $client_id,
            'grant_type' => $grant_type,
            'response_type' => $response_type,
            'scope' => $scope,
            'state' => $state,
        ));

        $error = null;

        $oAuthLoginConfig = $this->config['login_config'];
        $oAuthLoginParams = ArrayUtils::merge($this->config['login_params'], $this->config['authentication_client_credentials']);

        if (!$session) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $values = $form->getData();

                    if (is_array($values)) {
                        if (array_key_exists('username', $values) && array_key_exists('password', $values)) {
                            $credentials = array(
                                'username' => $values['username'],
                                'password' => $values['password'],
                            );
                            $oAuthSessionAdapter = $this->getOAuthSessionAdapter();
                            $oAuthSessionAdapter->setParams($credentials, $oAuthLoginConfig, $oAuthLoginParams);
                            $result = $auth->authenticate($oAuthSessionAdapter);

                            $session = $auth->getIdentity();
                            $error = $result->getMessages();

                        }
                    }
                }
            }
            $viewModel->setVariables(array(
                'error' => $error,
            ));
        }

        $session = $auth->getIdentity();
        $viewModel->setVariables(array(
            'form' => $form,
            'session' => $session,
            'error' => $error,
            'type' => $type,    
        ));
        return $viewModel;
    }

    public function grantTokenAction()
    {
        $oAuth2Service = $this->getOAuth2Service();
        $oAuth2Server = $oAuth2Service->getServer();
        $oAuth2Response = $oAuth2Service->getResponse();
        $oAuth2Request = $oAuth2Service->getRequest(); 

        try {
            $response = $oAuth2Server->handleTokenRequest($oAuth2Request, $oAuth2Response);
            $grantAccessToken = $oAuth2Server->grantAccessToken($oAuth2Request, $oAuth2Response);

            $statusCode = $response->getStatusCode();

            error_log($statusCode);

            $result = ($statusCode < 400 && $statusCode > 199) ? $grantAccessToken : array('error' => 'Invalid username and password combination');

        } catch (\Exception $e) {
            error_log($e->getMessage());
            $result = array('error' => $e->getMessage());
        }
        return new JsonModel($result);
    }
}
