<?php
namespace Authentication\Service;

use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Memory;
use OAuth2\OpenID\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\Request as OAuth2Request;

interface OAuth2ServiceInterface
{
    public function getServer();

    public function getResponse();

    public function getRequest();
}