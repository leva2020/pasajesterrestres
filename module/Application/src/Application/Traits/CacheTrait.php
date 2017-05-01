<?php
namespace Application\Traits;

trait CacheTrait
{

    protected $cache;

    protected function getEventCache($params)
    {
        $results = $this->cache->getEventManager()->trigger('get.pre', $this, $params);
        
        return ! $results->stopped() ? false : $results->last();
    }

    protected function setEventCache($params, $result, $ttl = false)
    {
        if ($ttl)
            $params['ttl'] = $ttl;
        $params['__RESULT__'] = $result;
        $this->cache->getEventManager()->trigger('get.post', $this, $params);
    }
}
