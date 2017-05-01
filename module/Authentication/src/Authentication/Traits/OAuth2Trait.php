<?php
namespace Authentication\Traits;

trait OAuth2Trait
{

    public function getOAuth2Pdo()
    {
        return $this->getServiceLocator()->get('OAuth2Pdo');
    }

    public function getOAuth2Service()
    {
        return $this->getServiceLocator()->get('Authentication\Service\OAuth2Service');
    }

    private function getOAuthSessionAdapter()
    {
        return $this->getServiceLocator()->get('Authentication\Model\OAuthSessionAdapter');
    }

        private function getGoogleSessionAdapter()
    {
        return $this->getServiceLocator()->get('Authentication\Model\GoogleSessionAdapter');
    }
}
