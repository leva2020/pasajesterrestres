<?php
namespace Authentication\Service;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Memory;
use OAuth2\OpenID\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\Request as OAuth2Request;

class OAuth2Service implements OAuth2ServiceInterface
{

    private $oAuth2server;
    private $oAuth2Response;
    private $oAuth2Request;

    public function __construct($storage)
    {
        $grantTypes = array(
            'authorization_code' => new AuthorizationCode($storage),
            'user_credentials' => new UserCredentials($storage),
            'refresh_token' => new RefreshToken($storage, array(
                'always_issue_new_refresh_token' => true
            ))
        );
        
        $this->oAuth2server = new OAuth2Server($storage, array(
            'enforce_state' => true,
            'allow_implicit' => true,
            'use_openid_connect' => true,
            'issuer' => $_SERVER['HTTP_HOST']
        ), $grantTypes);

        $this->oAuth2Response = new BridgeResponse();

        $this->oAuth2Request = OAuth2Request::createFromGlobals();
    }

    public function getServer() 
    {
        return $this->oAuth2server;
    }

    public function getResponse()
    {
        return $this->oAuth2Response;
    }

    public function getRequest()
    {
        return $this->oAuth2Request;
    }
}
