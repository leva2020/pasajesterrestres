<?php
namespace Authentication\Model;

class OAuthTools
{

    /**
     *
     * @internal creada para facilitar migracion usuarios sitios drupal, para unificarse deben crearse nuevamente credenciales de clientes con nuevo hash
     *          
     * @param string $secret            
     * @param string $salt            
     * @return string
     */
    public static function oAuthHash($secret, $salt)
    {
        return hash('sha256', hash('sha256', md5($secret)) . $salt);
    }

    /**
     * Salt generator
     *
     * @return string
     */
    public static function genSalt()
    {
        // return md5($username);
        return md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
    }

    /**
     * Checks the password.
     *
     * @param string $secret            
     * @param string $pass            
     * @param string $salt            
     */
    public function checkPassword($secret, $pass, $salt)
    {
        return $pass == self::oAuthHash($secret, $salt);
    }
}
