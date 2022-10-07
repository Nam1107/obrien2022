<?php
require './database/db.php';
require './helper/middleware.php';
require './helper/validateUser.php';

class Auth
{
    public static function authenToken()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }


        if (empty($_COOKIE['token'])) {
            return null;
        } else {
            $token = $_COOKIE['token'];
        }

        $result   = custom("select user.* from user, login_token where user.ID = login_token.userID and login_Token.token = '$token'");

        if ($result != null && count($result) > 0) {
            $_SESSION['user'] = $result[0];

            return $result[0];
        }

        return null;
    }

    public static function Logout()
    {
        userOnly();
        session_destroy();
        if (!isset($_COOKIE['token'])) exit();

        $token['token'] = $_COOKIE['token'];

        delete('login_token', $token);
        setcookie('token', '', time() - 7 * 24 * 60 * 60, '/');
        $res['msg'] = 'You have successfully logout';
        $res['status'] = 1;
        dd($res);
        exit();
    }

    public static function Login()
    {
        guestsOnly();
        $errors = validateLogin($_POST);
        $res['status'] = 0;
        if (count($errors) === 0) {

            $user = selectOne('user', ['email' => $_POST['email']]);

            if (!$user) {
                array_push($errors, 'Email address does not exist');
            } elseif (password_verify($_POST['password'], $user['password'])) {
                $login_token['token'] = md5Security($user['email'] . time() . $user['ID']);
                $login_token['userID'] = $user['ID'];
                setcookie('token', $login_token['token'], time() + 7 * 24 * 60 * 60, '/');
                create('login_token', $login_token);
                $_SESSION['user'] = $user;
                $res['msg'] = 'login success';
                $res['status'] = 1;
                dd($res);
                exit();
            } else {
                array_push($errors, 'Wrong password');
            }
        }

        $res['errors'] = $errors;
        dd($res);
        exit();
    }
    public static function Register()
    {
        guestsOnly();
        $table = 'user';
        $res['status'] = 0;
        $errors = validateRegister($_POST);
        if (count($errors) === 0) {
            $_POST['role'] = '0';
            $_POST['avatar'] = 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg';
            $_POST['createdAt'] = currentTime();
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user_id = create($table, $_POST);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else $res['errors'] = $errors;


        dd($res);
        exit();
    }
}