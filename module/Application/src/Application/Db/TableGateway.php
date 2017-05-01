<?php
namespace Application\Db;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use Zend\Db\TableGateway\Feature\MasterSlaveFeature;
use Zend\Db\TableGateway\Feature\FeatureSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\TableGateway\Feature\AbstractFeature;
use Application\Model\EventFeatureCacheAwareInterface;
use Zend\Db\TableGateway\Feature\EventFeature;

class TableGateway extends ZendTableGateway implements EventFeatureCacheAwareInterface
{

    protected $eventFeature;

    /**
     * Constructor
     *
     * @param string $table            
     * @param AdapterInterface $adapter            
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features            
     * @param ResultSetInterface $resultSetPrototype            
     * @param Sql $sql            
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($table, AdapterInterface $adapter, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null, $slaveDatabase = null)
    {
        // table
        if (! (is_string($table) || $table instanceof TableIdentifier)) {
            throw new \InvalidArgumentException('Table name must be a string or an instance of Zend\Db\Sql\TableIdentifier');
        }
        $this->table = $table;
        
        // adapter
        $this->adapter = $adapter;
        
        // process features
        if ($features !== null) {
            if ($features instanceof AbstractFeature) {
                $features = array(
                    $features
                );
            }
            if (is_array($features)) {
                $this->featureSet = new FeatureSet($features);
            } elseif ($features instanceof FeatureSet) {
                $this->featureSet = $features;
            } else {
                throw new \InvalidArgumentException('TableGateway expects $feature to be an instance of an AbstractFeature or a FeatureSet, or an array of AbstractFeatures');
            }
        } else {
            $this->featureSet = new FeatureSet();
        }
        
        $this->featureSet->addFeature(new MasterSlaveFeature($slaveDatabase));
        
        // result prototype
        $this->resultSetPrototype = ($resultSetPrototype) ?  : new ResultSet();
        
        // Sql object (factory for select, insert, update, delete)
        $this->sql = ($sql) ?  : new Sql($this->adapter, $this->table);
        
        // check sql object bound to same table
        if ($this->sql->getTable() != $this->table) {
            throw new \InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }
        
        $this->initialize();
    }

    /*
     * (non-PHPdoc)
     * @see \Application\Model\EventFeatureCacheAwareInterface::setEventFeatureCache()
     */
    public function setEventFeatureCache(EventFeature $eventFeature)
    {
        if ($eventFeature !== null) {
            if ($eventFeature instanceof AbstractFeature) {
                $eventFeature = array(
                    $eventFeature
                );
            }
            if (is_array($eventFeature)) {
                $this->featureSet = new FeatureSet($eventFeature);
            } elseif ($eventFeature instanceof FeatureSet) {
                $this->featureSet = $eventFeature;
            } else {
                throw new \InvalidArgumentException('TableGateway expects $eventFeature to be an instance of an AbstractFeature or a FeatureSet, or an array of AbstractFeatures');
            }
        }
        $this->eventFeature = $eventFeature;
    }
}