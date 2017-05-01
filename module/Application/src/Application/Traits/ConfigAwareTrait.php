<?php
namespace Application\Traits;

use Zend\Stdlib\ArrayUtils;

trait ConfigAwareTrait
{

    protected $config;

    public function setConfig($config)
    {
        if (is_array($this->config)) {
            $this->config = ArrayUtils::merge($this->config, $config);
        } else
            $this->config = $config;
    }
}
