<?php
namespace Application\Db;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

class MasterSlaveAdapterFactory implements FactoryInterface
{

    protected $key;

    protected $config;

    protected $default;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $this->config = $config['main_config_values'];
        $this->default = $config[$this->key];
        
        $randomDatabase = $this->randomSelectDatabase();
        return $randomDatabase;
    }

    protected function randomSelectDatabase()
    {
        $keys = array_keys($this->default);
        $randomIndex = rand(0, count($this->default) - 1);
        $selectedDriver = $this->default[$keys[$randomIndex]];
        
        $adapter = new Adapter($selectedDriver);
        
        return $adapter;
    }
}
