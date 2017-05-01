<?php
namespace Authentication\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;

class OAuthAccessTokenTable
{

    protected $tableGateway;

    protected $featureSet;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->featureSet = $this->tableGateway->getFeatureSet()->getFeatureByClassName('Zend\Db\TableGateway\Feature\EventFeature');
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function get($accessToken)
    {
        // @todo revisar como obtener datos en memcached para request via CURL
        // $params = compact('accessToken');
        // $results = $this->featureSet->getEventManager()->trigger('get.pre', $this, $params);
        // // If an event stopped propagation, return the value
        // if ($results->stopped()) {
        // return $results->last();
        // }
        $rowset = $this->tableGateway->select(array(
            'access_token' => $accessToken
        ));
        $row = $rowset->current();
        
        // $params['__RESULT__'] = $row;
        // $this->featureSet->getEventManager()->trigger('get.post', $this, $params);
        return ! $row ? false : $row;
    }

    public function getByUserId($userId)
    {
        $params = compact('userId');
        $results = $this->featureSet->getEventManager()->trigger('get.pre', $this, $params);
        // If an event stopped propagation, return the value
        if ($results->stopped()) {
            return $results->last();
        }
        
        $resultSet = $this->tableGateway->select(array(
            'user_id' => $userId
        ));
        
        $params['__RESULT__'] = $resultSet;
        $this->featureSet->getEventManager()->trigger('get.post', $this, $params);
        return $resultSet;
    }

    public function save(OAuthAccessToken $oAuthAccessToken)
    {
        $data = array(
            'access_token' => $oAuthAccessToken->getAccessToken(),
            'client_id' => $oAuthAccessToken->getClientId(),
            'user_id' => $oAuthAccessToken->getUserId(),
            'expires' => $oAuthAccessToken->getExpires(),
            'scope' => $oAuthAccessToken->getScope()
        );
        
        $accessToken = $oAuthAccessToken->getAccessToken();
        
        if ($accessToken === 0 || ! $this->get($accessToken)) {
            $result = $this->tableGateway->insert($data) > 0 ? true : false;
        } else {
            if ($this->get($accessToken)) {
                unset($data['expires']);
                $result = $this->tableGateway->update($data, array(
                    'access_token' => $accessToken
                )) > 0 ? true : false;
            } else {
                $result = false;
            }
        }
        return $result;
    }

    public function update(OAuthAccessToken $oAuthAccessToken)
    {
        $data = array(
            'user_id' => $oAuthAccessToken->getUserId()
        );
        
        $accessToken = $oAuthAccessToken->getAccessToken();
        $this->tableGateway->update($data, array(
            'access_token' => $accessToken
        ));
        
        return $accessToken;
    }

    public function delete($accessToken)
    {
        $this->tableGateway->delete(array(
            'access_token' => $accessToken
        ));
    }

    public function getExpiredTokens()
    {
        $currentTime = date('Y-m-d H:i:s', time());
        
        $select = new Select();
        $select->from($this->tableGateway->getTable());
        $select->where->lessThan('expires', $currentTime);
        $select->limit(1000);
        
        $result = $this->tableGateway->selectWith($select);
        
        return $result;
    }

    public function deleteExpiredTokens()
    {
        $currentTime = date('Y-m-d H:i:s', time());
        
        $delete = new Delete();
        $delete->from($this->tableGateway->getTable());
        $delete->where->lessThan('expires', $currentTime);
        
        $result = $this->tableGateway->deleteWith($delete);
        
        return $result;
    }

    public function getDriver()
    {
        return $this->tableGateway->adapter->driver;
    }
}
