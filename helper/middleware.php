<?php
function md5Security($pwd)
{
    return md5(md5($pwd) . MD5_PRIVATE_KEY);
}

function authenToken()
{
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }


    if (empty($_COOKIE['token'])) {
        return null;
    } else {
        $token = $_COOKIE['token'];
    }

    $result   = custom("select user.* from user, login_token where user.ID = login_token.userID and login_token.token ='$token'");

    if ($result != null && count($result) > 0) {
        $_SESSION['user'] = $result[0];

        return $result[0];
    }

    return null;
}

function checkRequest($req)
{
    if ($_SERVER['REQUEST_METHOD'] !== $req) {
        $res['status'] = 0;
        $res['msg'] = 'Wrong method';
        dd($res);
        exit();
    }
}

function userOnly()
{
    $table = 'user';
    if (!authenToken()) {
        $res['msg'] = 'You need to login first';
        $res['status'] = '0';
        dd($res);
        exit();
    } else {
        $id = $_SESSION['user']['ID'];
        unset($_SESSION['user']);
        $obj = selectOne($table, ['ID' => $id]);
        if (!$obj) {
            $res['msg'] = 'Not found your account';
            $res['status'] = '0';
            dd($res);
            exit();
        } else $_SESSION['user'] = $obj;
    }
}
function adminOnly()
{
    userOnly();
    if ($_SESSION['user']['role'] != 1) {
        $res['msg'] = 'You are not admin';
        $res['status'] = '0';
        dd($res);
        exit();
    }
}
function guestsOnly()
{
    if (authenToken()) {
        $res['msg'] = 'You have logged in';
        $res['status'] = '0';
        dd($res);
        exit();
    }
}