<?php
namespace Application\Traits;

trait ModuleTablesTrait
{

    private function getPaymentsTable()
    {
        return $this->getServiceLocator()->get('Application\Model\PaymentsTable');
    }

    private function getCitiesTable()
    {
        return $this->getServiceLocator()->get('Application\Model\CitiesTable');
    }
}


