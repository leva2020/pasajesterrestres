<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Authentication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Authentication;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\ArrayUtils;
use OAuth2\Storage\Pdo as OAuth2Pdo;
use OAuth2\Server as OAuth2Server;
use Application\Interfaces\ConfigAwareInterface;
use Authentication\Service\OAuth2Service;
use Authentication\Model\OAuthSessionAdapter;
use Authentication\Model\OAuthPdoStorage;
use Authentication\Model\OAuthAccessTokenTable;
use Authentication\Model\OAuthAccessToken;
use Authentication\Model\GoogleSessionAdapter;
use Authentication\Model\FacebookSessionAdapter;

class Module implements AutoloaderProviderInterface
{
    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof ConfigAwareInterface) {
                        $locator = $sm->getServiceLocator();
                        $config = $locator->get('Config');
                        $oAuthConfig = $config['oauth2_config'];
                        $instance->setConfig($oAuthConfig);
                    }
                }
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
        );
    }

    public function getConfig()
    {
        $module = include __DIR__ . '/config/module.config.php';
        $params = include __DIR__ . '/config/params.config.php';
        
        return ArrayUtils::merge($module, $params);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Authentication\Model\OAuthAccessTokenTable' => function ($sm) {
                $tableGateway = $sm->get('OAuthAccessTokenTableGateway');
                $table = new OAuthAccessTokenTable($tableGateway);
                return $table;
                },
                'OAuthAccessTokenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OAuthAccessToken());
                    return new TableGateway('oauth_access_tokens', $dbAdapter, null, $resultSetPrototype);
                },
                'OAuth2PdoStorage' => function ($sm) {
                    $config = $sm->get('config');
                    $pdoConnection = $config['db'];

                    $storageConfig = array('user_table' => 'users');
                    $oAuthPdo = new OAuthPdoStorage($pdoConnection, $storageConfig);
                    return $oAuthPdo;
                },
                'Authentication\Service\OAuth2Service' => function ($sm) {
                    $storage = $sm->get('OAuth2PdoStorage');
                    $service = new OAuth2Service($storage);
                    return $service;
                },
                'Authentication\Model\OAuthSessionAdapter' => function ($sm) {
                    $userTable = $sm->get('Register\Model\UserTable');
                    $oAuthAccessTokenTable = $sm->get('Authentication\Model\OAuthAccessTokenTable');
                    $oAuthSessionAdapter = new OAuthSessionAdapter($userTable, $oAuthAccessTokenTable);
                    return $oAuthSessionAdapter;
                },
                'Authentication\Model\FacebookSessionAdapter' => function ($sm) {
                    $facebookSessionAdapter = new FacebookSessionAdapter($sm);
                    return $facebookSessionAdapter;
                },
                'Authentication\Model\GoogleSessionAdapter' => function ($sm) {
                    $googleSessionAdapter = new GoogleSessionAdapter($sm);
                    return $googleSessionAdapter;
                },
            )
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
