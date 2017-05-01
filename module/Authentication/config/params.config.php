<?php
$env = getenv('APPLICATION_ENV') ?  : 'production';

$scheme = (isset($_SERVER['HTTPS']) && ! empty($_SERVER['HTTPS'])) ? 'https' : 'http';
$baseDomain = $scheme . "://" . $_SERVER['HTTP_HOST'];

return array(
    'oauth2_config' => array(
        'authentication_client_credentials' => array(
            'client_id' => 'pasajes',
            'client_secret' => 'mTOANBWqTQQvDMwLkHqQ'
        ),
        'login_config' => array(
            'base_uri' => $baseDomain . '/',
            'access_token_uri' => 'auth/grant-token',
            'base_domain' => $_SERVER['SERVER_NAME'],
            'authorize_uri' => $baseDomain . '/auth/authorize/'
        ),
        'login_params' => array(
            'grant_type' => 'password',
            'cookie_support' => 'false',
            'scope' => 'login'
        ),
        'facebook_login' => array(
            'application_id' => '621809747895524', // id de producción
            'default_status' => 1
        ),
        'google_login' => array(
            'application_name' => 'PASAJESTERRESTRES.COM.CO',
            'application_id' => '267264559648-k755soodak54p9nvj7q8gm9242inaqnh.apps.googleusercontent.com',
            'application_secret' => 'G2wDg4R9U-vqW-YuVEX8-9ib',
            'default_status' => 1
        ),
        'login_grant_server_config' => array(
            'access_lifetime' => 2419200
        ), // tiempo de vida de token de sesion
    )
);