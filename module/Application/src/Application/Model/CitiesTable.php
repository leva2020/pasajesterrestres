<?php
namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Predicate\Predicate;

class CitiesTable
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

    public function getById($id)
    {
        try {
            $rowset = $this->tableGateway->select(array('id' => $id));
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        }

        $row = $rowset->current();
        return ! $row ? false : $row;
    }

    public function getIdRapidoOchoa($id)
    {
        try {
            $select = new Select();
            $select->getSqlString();
            $select->from($this->tableGateway->getTable());
            $select->columns(array('id_rapido_ochoa'));
            $select->where(array('id' => (int)$id), Predicate::OP_OR);
            $rowset = $this->tableGateway->selectWith($select);
//             $rowset = $this->tableGateway->select(function (Select $select) use ($id) {
//                  $select->columns(array('id_rapido_ochoa'));
//                  $select->where(array('id' => (int)$id));
//             });
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        }

        $row = $rowset->toArray();
        return ! $row ? false : $row;
    }

    public function getByName($name)
    {
        try {
            $rowset = $this->tableGateway->select(array('name' => $name));
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        }

        $row = $rowset->current();
        return ! $row ? false : $row;
    }

    public function save(Cities $cities)
    {
        $data = array(
            'country_id' => $cities->getCountryId(),
            'name' => $cities->getName(),
            'id_rapido_ochoa' => $cities->getIdRapidoOchoa()
        );

        $id = $cities->getId();
        $idRapidoOchoaBD = $this->getIdRapidoOchoa($id);
        $idRapidoOchoaBD = $idRapidoOchoaBD[0]['id_rapido_ochoa'];

        $dataBD = $this->getById($id);

        if ($dataBD == false) {
            $this->tableGateway->insert($data);
        } else {
//             if ($this->getById($idRapidoOchoa)) {
//                 $this->tableGateway->update(array('id_rapido_ochoa' => $cities->getIdRapidoOchoa()), array('id' => $id));
            $this->tableGateway->update($data, array('id' => $id));
//             } else {
//                 throw new \Exception('Orden no existe');
//             }
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}