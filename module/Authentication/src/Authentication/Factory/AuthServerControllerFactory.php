<?php
namespace Authentication\Factory;

use Authentication\Controller\AuthServerController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
*
*/
class AuthServerControllerFactory implements FactoryInterface
{
  
    /**
      * Create service
      *
      * @param ServiceLocatorInterface $serviceLocator
      *
      * @return mixed
      */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $oAuth2Service = $realServiceLocator->get('Authentication\Service\OAuth2Service');

        return new AuthServerController($oAuth2Service);
    }
}
