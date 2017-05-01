<?php
namespace Application\Traits;

trait IntegrationTrait
{

    private function getRapidoOchoaIntegration()
    {
        return $this->getServiceLocator()->get('Application\Model\RapidoOchoaIntegration');
    }
}


