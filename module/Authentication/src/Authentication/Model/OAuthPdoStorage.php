<?php
namespace Authentication\Model;

use OAuth2\Storage\Pdo;
use Authentication\Model\OAuthTools;

class OAuthPdoStorage extends Pdo
{
        public function getUser($username)
    {
        $stmt = $this->db->prepare($sql = sprintf('SELECT * from %s where email=:email', $this->config['user_table']));
        $stmt->execute(array('email' => $username));

        if (!$userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        // the default behavior is to use "username" as the user_id
        return array_merge(array(
            'user_id' => $userInfo['id']
        ), $userInfo);
    }

    public function checkClientCredentials($client_id, $client_secret = null)
    {   
        return true;
    }

        /* UserCredentialsInterface */
    public function checkUserCredentials($username, $password)
    {
        $result = $this->getUser($username);
        
        error_log("result: " . json_encode($result));

        $tools = new OAuthTools();
        
        return $tools->checkPassword($password, $result['password'], $result['salt']) ? $result : FALSE;
    }
}