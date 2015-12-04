<?php

namespace Florence;

use Slim\Slim;
use Florence\User;
use Illuminate\Database\QueryException;

class Authorization
{
    public static function isAuthorised($app, $token)
    {
        if (!$token) {
            $app->halt(401, json_encode(['status' => 401, 'message' => 'Token required']));
        }
        return self::isValid($app, $token);
    }

    /**
    * @param $username
    * @param $password
    */
    public function isValid($app, $token)
    {
        try {
            $user = User::where('token', $token)->first();
            if (! empty($user)) {
                $expiry = self::isTokenExpired($token);
                    if($expiry == true) {
                        $app->halt(401, json_encode(['status'=> 401, 'message' => 'Session expired']));
                    } else {
                        $status = json_encode(['status'=>200,
                        'username'      =>  $user['username'],
                        'password'      =>  $user['password'],
                        'token'         => $user['token'],
                        'token_expire'  => $user['token_expire']
                        ]);
                    }
            } else {
                $app->halt(401, json_encode(['status'=> 401, 'message' => 'Session expired']));
            }
        } catch(QueryException $e) {
            $app->halt(401, json_encode(['status'=> 401, 'message' => 'Session expired']));
        }
        return $status;
    }

    public function isTokenExpired($token)
    {
        $user = User::where('token', $token)->first();

        $token_expire = $user['token_expire'];
        $currTime = date('Y-m-d H:i:s');

        if($token_expire < $currTime) {
            return true;
        }
        return false;
    }
}