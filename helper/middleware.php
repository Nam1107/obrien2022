<?php

require_once './src/JWT.php';
require_once './src/Key.php';
require_once './src/SignatureInvalidException.php';
require_once './src/ExpiredException.php';


use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class middleware extends Controllers
{
    public $user_model;
    public function __construct()
    {
        $this->user_model = $this->model('userModel');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }

    function md5Security($pwd)
    {
        return md5(md5($pwd) . MD5_PRIVATE_KEY);
    }

    function authenToken()
    {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            session_destroy();
            $res['status'] = 0;
            $res['errors'] = 'You need a token to access';
            return $res;
            // $this->loadErrors(400, 'You need a token to access');
        }
        $token = $headers['Authorization'];
        $check = explode(" ", $token);
        try {
            $jwt = JWT::decode($check[1],  new Key(TOKEN_SECRET, 'HS256'));
            $data = json_decode(json_encode($jwt), true);
            $id = $data['data']['id'];
            $obj = $this->user_model->getDetail($id);

            if (!$obj) {
                $res['status'] = 0;
                $res['errors'] = 'Not found user';
            }
            $_SESSION['user'] = $obj;
            $res['status'] = 1;
            $res['obj'] = $obj;
            return $res;
        } catch (Exception $e) {
            $res['status'] = 0;
            $res['errors'] = $e->getMessage();
            return $res;
        }
    }

    function checkRequest($req)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $req) {
            $this->loadErrors(400, 'Wrong method');
        }
    }

    function userOnly()
    {

        $obj = $this->authenToken();
        if ($obj['status'] == 0) {
            $this->loadErrors(400, $obj['errors']);
        }
    }
    function shipperOnly()
    {
        $this->userOnly();
        if ($_SESSION['user']['role'] == 'ROLE_USER') {
            $this->loadErrors(400, 'you do not have permission to access');
        }
    }
    function adminOnly()
    {
        $this->userOnly();
        if ($_SESSION['user']['role'] != 'ROLE_ADMIN') {
            $this->loadErrors(400, 'you not have permission to access');
        }
    }
    function guestsOnly()
    {
        if (isset($_SESSION['userID'])) {
            $this->loadErrors(400, 'You have logged in');
        }
    }
}