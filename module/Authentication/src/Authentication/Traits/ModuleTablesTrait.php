<?php
namespace Authentication\Traits;

trait ModuleTablesTrait
{

    public function getOAuthAccessTokenTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthAccessTokenTable');
    }

    public function getOAuthAuthorizationCodeTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthAuthorizationCodeTable');
    }

    public function getOAuthClientTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthClientTable');
    }

    public function getOAuthRefreshTokenTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthRefreshTokenTable');
    }

    public function getOAuthSessionTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthSessionTable');
    }

    public function getSessionHistoryTable()
    {
        return $this->getServiceLocator()->get('Authentication\Model\SessionHistoryTable');
    }
}
