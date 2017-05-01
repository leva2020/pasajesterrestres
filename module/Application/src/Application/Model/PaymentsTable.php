<?php
namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;

class PaymentsTable
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
    
    public function get($id_reference)
    {
        $id_reference  = $id_reference;
        try {
            $rowset = $this->tableGateway->select(array('id_reference' => $id_reference));
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        }
        
        $row = $rowset->current();
        return ! $row ? false : $row;
    }
    
    public function save(Payments $payments)
    {
        $data = array(
            'payment_types_id' => $payments->getPayment_types_id(),
            'base_amount' => $payments->getBase_amount(),
            'id_reference' => $payments->getId_reference(),
            'id_client' => $payments->getId_client(),
            'paymethod' => $payments->getPaymethod(),
            'reference' => $payments->getReference(),
            'shopper_email' => $payments->getShopper_email(),
            'shopper_name' => $payments->getShopper_name(),
            'tax_amount' => $payments->getTax_amount(),
            'token' => $payments->getToken(),
            'token_transaction_code' => $payments->getToken_transaction_code(),
            'total_amount' => $payments->getTotal_amount(),
            'transaction_code' => $payments->getTransaction_code(),
            'transaction_id' => $payments->getTransaction_id(),
            'order_id' => $payments->getOrder_id(),
            'transaction_message' => $payments->getTransaction_message(),
        );
    
        $id_reference = $payments->getId_reference();
        if ($id_reference == '0') {
            $this->tableGateway->insert($data);
        } else {
            if ($this->get($id_reference)) {
                $this->tableGateway->update(array('transaction_code' => $payments->getTransaction_code()), array('id_reference' => $id_reference));
            } else {
                throw new \Exception('Orden no existe');
            }
        }
    }
    
    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
    
}