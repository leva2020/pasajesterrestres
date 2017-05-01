<?php
namespace Authentication\Model;

class OAuthAccessToken
{

    private $accessToken;

    private $clientId;

    private $userId;

    private $expires;

    private $scope;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        if (array_key_exists('access_token', $data))
            $this->setAccessToken($data['access_token']);
        if (array_key_exists('client_id', $data))
            $this->setClientId($data['client_id']);
        if (array_key_exists('user_id', $data))
            $this->setUserId($data['user_id']);
        if (array_key_exists('expires', $data))
            $this->setExpires($data['expires']);
        if (array_key_exists('scope', $data))
            $this->setScope($data['scope']);
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
    {
        throw new \Exception("Not used");
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function setExpires($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }
}