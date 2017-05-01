<?php
namespace Register\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Predicate\Predicate;

class UserTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        return ! $row ? false : $row;
    }

    public function save(User $user)
    {
        $data = array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'register_date' => date("Y-m-d H:i:s", time()),
            'update_date' => date("Y-m-d H:i:s", time()),
            'uid' => $user->getUid(),
            'status' => $user->getStatus(),
            'document_type_id' => $user->getDocumentTypeId(),
            'user_types_id' => $user->getUserTypeId()
        );
        
        $id = (int) $user->getId();
        
        if ($id == 0) {
            $user->encryptSecret();
            $data['password'] = $user->getPassword();
            $data['salt'] = $user->getSalt();
            $this->tableGateway->insert($data);
            
            $id = (int) $this->tableGateway->getLastInsertValue();
            
        } else {
            if ($this->get($id)) {
                unset($data['username']);
                unset($data['email']);
                unset($data['user_types_id']);
                unset($data['register_date']);
                if (! isset($data['status']) || $data['status'] == null) {
                    unset($data['status']);
                }
                
                $this->tableGateway->update($data, array(
                    'id' => $id
                ));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
        return $id;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array(
            'id' => (int) $id
        ));
    }

    public function getByUserName($username)
    {
        $rowset = $this->tableGateway->select(array(
            'username' => $username
        ));
        $row = $rowset->current();
        
        return ! $row ? false : $row;
    }

        public function getByUsernameOrEmail($usernameOrEmail)
    {
        $select = new Select();
        $select->from($this->tableGateway->getTable());
        $select->where(array(
            'email' => $usernameOrEmail,
            'username' => $usernameOrEmail
        ), Predicate::OP_OR);
        
        $rowset = $this->tableGateway->selectWith($select);
        $row = $rowset->current();
       
        return ! $row ? false : $row;
    }

    /**
     *
     * @param string $email            
     * @return Ambigous <boolean, mixed>
     */
    public function getByEmail($email)
    {
        $rowset = $this->tableGateway->select(array(
            'email' => $email
        ));
        $row = ($email != null) ? $rowset->current() : false;
                
        return ! $row ? false : $row;
    }

    /**
     *
     * @param unknown_type $uid            
     * @return Ambigous <boolean, mixed>
     */
    public function getByUid($uid)
    {
        $rowset = $this->tableGateway->select(array(
            'uid' => $uid
        ));
        $row = $rowset->current();
        
        return ! $row ? false : $row;
    }

}