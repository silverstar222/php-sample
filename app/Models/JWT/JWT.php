<?php

namespace App\Models\JWT;

use Firebase\JWT\JWT as JWTFirebase;

class JWT
{
    private const key='92c8b10157e05856af182a643de7dcea14472f74';

    /**
     * @param int $users_id
     * @param int $users_refresh_tokens_id
     *
     * @return string
     */
    public static function generateToken(int $users_id, int $users_refresh_tokens_id):string
    {
        $payload = [
            'rt_id' => $users_refresh_tokens_id,
            'iss' => $users_id,
            'exp' => time() + 84600,
            'nbf' => time()
        ];
        JWTFirebase::$timestamp = time();
        JWTFirebase::$leeway = 84600;
        $jwt = JWTFirebase::encode($payload,self::key);

        return $jwt;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public static function checkValidToken(string $token):array
    {
        try
        {
            $token = JWTFirebase::decode($token,self::key);
        } catch (\Exception $exception)
        {
            switch ($exception->getMessage())
            {
                case 'Expired token' :
                    return ['success' => false, 'message' => 'Expired token'];
                    break;

                default :
                    return ['success' => false, 'message' => 'Invalid token'];
                    break;
            }
        }

        return ['success' => true, 'payload' => $token];
    }
}
