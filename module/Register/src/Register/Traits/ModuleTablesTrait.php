<?php
namespace Register\Traits;

trait ModuleTablesTrait
{

    private function getUserTable()
    {
        return $this->getServiceLocator()->get('Register\Model\UserTable');
    }
}


