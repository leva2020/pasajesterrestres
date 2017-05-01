<?php
namespace Application\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Cache\Storage\Adapter\Memcached;

class CacheListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    protected $cache;

    /**
     *
     * @param Memcached $cache            
     */
    public function __construct(Memcached $cache)
    {
        $this->cache = $cache;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\EventManager\ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('get.pre', array(
            $this,
            'load'
        ), 100);
        $this->listeners[] = $events->attach('get.post', array(
            $this,
            'save'
        ), - 100);
        $this->listeners[] = $events->attach('save.post', array(
            $this,
            'remove'
        ), - 100);
    }

    /**
     *
     * @param EventInterface $e            
     * @return unknown
     */
    public function load(EventInterface $e)
    {
        $id = md5(get_class($e->getTarget()) . '-' . json_encode($e->getParams()));
        
        $content = false;
        try {
            if (false != ($content = $this->cache->getItem($id))) {
                $e->stopPropagation(true);
                
                $supportedDatatypes = $this->cache->getCapabilities()->getSupportedDatatypes();
                if (! $supportedDatatypes['object']) {
                    unserialize($content);
                }
                return $content;
            }
        } catch (\Exception $e) {
            error_log("error loading from memcached");
            error_log($e->getMessage());
        }
    }

    /**
     *
     * @param EventInterface $e            
     */
    public function save(EventInterface $e)
    {
        $params = $e->getParams();
        $content = $params['__RESULT__'];
        unset($params['__RESULT__']);
        
        $modifiedOptions = false;
        
        if (isset($params['ttl']) && is_numeric($params['ttl']) && $params['ttl'] > 0) {
            try {
                $options = $this->cache->getOptions();
                $defaultTtl = $options->getTtl();
                $options->setTtl($params['ttl']);
                $this->cache->setOptions($options);
                unset($params['ttl']);
                $modifiedOptions = true;
            } catch (\Exception $e) {
                error_log("error setting new ttl: " . $e->getMessage());
            }
        }
        
        $id = md5(get_class($e->getTarget()) . '-' . json_encode($params));
        
        try {
            $supportedDatatypes = $this->cache->getCapabilities()->getSupportedDatatypes();
            
            if ($supportedDatatypes['object']) {
                $setIt = $this->cache->setItem($id, $content);
            } else {
                $setIt = $this->cache->setItem($id, serialize($content));
            }
        } catch (\Exception $e) {
            error_log("error saving to memcached");
            error_log($e->getMessage());
        }
        
        if ($modifiedOptions && isset($defaultTtl)) {
            $options->setTtl($defaultTtl);
            $this->cache->setOptions($options);
        }
    }

    public function remove(EventInterface $e)
    {
        $params = $e->getParams();
        unset($params['__RESULT__']);
        
        $id = md5(get_class($e->getTarget()) . '-' . json_encode($params));
        
        $reIt = $this->cache->removeItem($id);
    }
}
