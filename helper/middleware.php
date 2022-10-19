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
        $res['errors'] = 'Wrong method';
        dd($res);
        exit();
    }
}

function userOnly()
{
    $table = 'user';
    if (!authenToken()) {
        $res['status'] = '0';
        $res['errors'] = 'You need to login first';

        dd($res);
        exit();
    } else {
        $id = $_SESSION['user']['ID'];
        unset($_SESSION['user']);
        $obj = selectOne($table, ['ID' => $id]);
        if (!$obj) {
            $res['status'] = '0';
            $res['errors'] = 'Not found your account';

            dd($res);
            exit();
        } else $_SESSION['user'] = $obj;
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
    if (authenToken()) {
        $res['status'] = '0';
        $res['errors'] = 'You have logged in';

        dd($res);
        exit();
    }
}