<?php
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Cities implements InputFilterAwareInterface
{

    private $id;

    private $country_id;

    private $name;

    private $id_rapido_ochoa;

    public function exchangeArray($data)
    {
        if (array_key_exists('id', $data))
            $this->setId($data['id']);
        if (array_key_exists('country_id', $data))
            $this->setCountryId($data['country_id']);
        if (array_key_exists('name', $data))
            $this->setName($data['name']);
        if (array_key_exists('id_rapido_ochoa', $data))
            $this->setIdRapidoOchoa($data['id_rapido_ochoa']);
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {}

    function getId()
    {
        return $this->id;
    }

    function getCountryId()
    {
        return $this->country_id;
    }

    function getName()
    {
        return $this->name;
    }

    function getIdRapidoOchoa()
    {
        return $this->id_rapido_ochoa;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setCountryId($countryId)
    {
        $this->country_id = $countryId;
    }

    function setName($name)
    {
        $this->name = $name;
    }

    function setIdRapidoOchoa($id_rapido_ochoa)
    {
        $this->id_rapido_ochoa = $id_rapido_ochoa;
    }
}