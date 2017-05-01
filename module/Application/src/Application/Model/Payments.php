<?php
namespace Application\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Payments implements InputFilterAwareInterface
{

    private $id;

    private $payment_types_id;

    private $base_amount;

    private $id_reference;

    private $id_client;

    private $paymethod;

    private $reference;

    private $shopper_email;

    private $shopper_name;

    private $tax_amount;

    private $token;

    private $token_transaction_code;

    private $total_amount;

    private $transaction_code;

    private $transaction_id;

    private $order_id;

    private $transaction_message;

    public function exchangeArray($data)
    {
        if (array_key_exists('id', $data))
            $this->setId($data['id']);
        if (array_key_exists('payment_types_id', $data))
            $this->setPayment_types_id($data['payment_types_id']);
        if (array_key_exists('base_amount', $data))
            $this->setBase_amount($data['base_amount']);
        if (array_key_exists('id_reference', $data))
            $this->setId_reference($data['id_reference']);
        if (array_key_exists('id_client', $data))
            $this->setId_client($data['id_client']);
        if (array_key_exists('paymethod', $data))
            $this->setPaymethod($data['paymethod']);
        if (array_key_exists('reference', $data))
            $this->setReference($data['reference']);
        if (array_key_exists('shopper_email', $data))
            $this->setShopper_email($data['shopper_email']);
        if (array_key_exists('shopper_name', $data))
            $this->setShopper_name($data['shopper_name']);
        if (array_key_exists('tax_amount', $data))
            $this->setTax_amount($data['tax_amount']);
        if (array_key_exists('token', $data))
            $this->setToken($data['token']);
        if (array_key_exists('token_transaction_code', $data))
            $this->setToken_transaction_code($data['token_transaction_code']);
        if (array_key_exists('total_amount', $data))
            $this->setTotal_amount($data['total_amount']);
        if (array_key_exists('transaction_code', $data))
            $this->setTransaction_code($data['transaction_code']);
        if (array_key_exists('transaction_id', $data))
            $this->setTransaction_id($data['transaction_id']);
        if (array_key_exists('transaction_message', $data))
            $this->setTransaction_message($data['transaction_message']);
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

    function getPayment_types_id()
    {
        return $this->payment_types_id;
    }

    function getBase_amount()
    {
        return $this->base_amount;
    }

    function getId_reference()
    {
        return $this->id_reference;
    }

    function getId_client()
    {
        return $this->id_client;
    }

    function getPaymethod()
    {
        return $this->paymethod;
    }

    function getReference()
    {
        return $this->reference;
    }

    function getShopper_email()
    {
        return $this->shopper_email;
    }

    function getShopper_name()
    {
        return $this->shopper_name;
    }

    function getTax_amount()
    {
        return $this->tax_amount;
    }

    function getToken()
    {
        return $this->token;
    }

    function getToken_transaction_code()
    {
        return $this->token_transaction_code;
    }

    function getTotal_amount()
    {
        return $this->total_amount;
    }

    function getTransaction_code()
    {
        return $this->transaction_code;
    }

    function getTransaction_id()
    {
        return $this->transaction_id;
    }

    function getOrder_id()
    {
        return $this->order_id;
    }

    function getTransaction_message()
    {
        return $this->transaction_message;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setPayment_types_id($payment_types_id)
    {
        $this->payment_types_id = $payment_types_id;
    }

    function setBase_amount($base_amount)
    {
        $this->base_amount = $base_amount;
    }

    function setId_reference($id_reference)
    {
        $this->id_reference = $id_reference;
    }

    function setId_client($id_client)
    {
        $this->id_client = $id_client;
    }

    function setPaymethod($paymethod)
    {
        $this->paymethod = $paymethod;
    }

    function setReference($reference)
    {
        $this->reference = $reference;
    }

    function setShopper_email($shopper_email)
    {
        $this->shopper_email = $shopper_email;
    }

    function setShopper_name($shopper_name)
    {
        $this->shopper_name = $shopper_name;
    }

    function setTax_amount($tax_amount)
    {
        $this->tax_amount = $tax_amount;
    }

    function setToken($token)
    {
        $this->token = $token;
    }

    function setToken_transaction_code($token_transaction_code)
    {
        $this->token_transaction_code = $token_transaction_code;
    }

    function setTotal_amount($total_amount)
    {
        $this->total_amount = $total_amount;
    }

    function setTransaction_code($transaction_code)
    {
        $this->transaction_code = $transaction_code;
    }

    function setTransaction_id($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    public function setOrder_id($order_id)
    {
        $this->order_id = $order_id;
    }

    function setTransaction_message($transaction_message)
    {
        $this->transaction_message = $transaction_message;
    }
}