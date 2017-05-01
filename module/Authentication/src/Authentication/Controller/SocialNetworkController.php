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
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Interfaces\ConfigAwareInterface;
use Application\Traits\ConfigAwareTrait;
use Authentication\Traits\OAuth2Trait;

class SocialNetworkController extends AbstractActionController implements ConfigAwareInterface
{
    use ConfigAwareTrait, OAuth2Trait;

    public function googleOauth2CallbackAction()
    {
        $code = $this->getRequest()->getQuery('code');
        $clientId = $this->params()->fromRoute('client_id', 'pasajesterrestres');
        $loginType = $this->params()->fromRoute('login_type', 'default');

        /**
         *
         * @internal La cabecera P3P se define para permitir la lectura de cookies en cross-domain para IE en todas sus versiones
         */
        $response = $this->getResponse();
        $response->getHeaders()->addHeaders(array(
            'P3P' => "CP='CAO PSA OUR'",
        ));

        $auth = new AuthenticationService();
        $session = $auth->getIdentity();

        $loginOptions = array(
            'client_id' => $clientId,
            'grant_type' => 'authorization_code',
            'response_type' => 'code',
            'scope' => 'basic',
            'state' => rand(1000000000, getrandmax()),
            'type' => $loginType
        );

        if ($session) {
            return $this->redirect()->toRoute('authentication/login', $loginOptions);
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        $view->setVariables(array(
            'code' => $code,
            'login_options' => $loginOptions,
            'session' => $session
        ));

        $googleSessionAdapter = $this->getGoogleSessionAdapter();
        $googleSessionAdapter->setClientParams($code, $clientId);

        $result = $auth->authenticate($googleSessionAdapter);
        $error = $result->getMessages();

        if ($result->getCode() < 0) {
            return $this->redirect()->toRoute('authentication/login', $loginOptions);
        }

        return $view;
    }
}


