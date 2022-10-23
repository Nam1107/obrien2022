<?php

require './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;

function md5Security($pwd)
{
    return md5(md5($pwd) . MD5_PRIVATE_KEY);
}

function authenToken()
{
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        $res['status'] = 0;
        $res['error'] = 'You need a token to access';
        session_destroy();
        return $res;
    }
    $token = $headers['Authorization'];
    $check = explode(" ", $token);
    try {
        $jwt = JWT::decode($check[1],  new Key(TOKEN_SECRET, 'HS256'));
        $data = json_decode(json_encode($jwt), true);
        $id = $data['data']['id'];
        $obj = custom("SELECT user.ID,user.role FROM user where ID = $id");
        $_SESSION['user'] = $obj[0];
        $res['status'] = 1;
        $res['obj'] = $obj[0];
        return $res;
    } catch (SignatureInvalidException) {
        $res['status'] = 0;
        $res['error'] = 'Token verification failed';
        session_destroy();
        return $res;
    } catch (ExpiredException) {
        $res['status'] = 0;
        $res['error'] = 'Expired token';
        session_destroy();
        return $res;
    }
}

function checkRequest($req)
{
    if ($_SERVER['REQUEST_METHOD'] !== $req) {
        $res['status'] = 0;
        $res['errors'] = 'Wrong method';
        dd($res);
        exit();
    }
}

function userOnly()
{
    $obj = authenToken();
    if ($obj['status'] == 0) {
        dd($obj);
        exit();
    }
}
function adminOnly()
{
    userOnly();
    if ($_SESSION['user']['role'] != 1) {
        $res['status'] = '0';
        $res['errors'] = 'You are not admin';
        dd($res);
        exit();
    }
}
function guestsOnly()
{
    if (isset($_SESSION['userID'])) {
        $res['status'] = '0';
        $res['errors'] = 'You have logged in';
        dd($res);
        exit();
    }
}