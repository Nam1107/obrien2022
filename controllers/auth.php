<?php
require './database/db.php';
require './helper/middleware.php';
require './helper/validateUser.php';

class Auth
{
    function Logout()
    {
        userOnly();
        if (isset($_SESSION['user'])) {
            session_destroy();
            $res['msg'] = 'You have successfully logout';
            $res['status'] = 1;
            dd($res);
            exit();
        } else {
            $res['msg'] = "You haven't logged";
            $res['status'] = 0;
            dd($res);
            exit();
        }
    }

    function Login()
    {
        guestsOnly();
        $errors = validateLogin($_POST);
        $res['status'] = 0;
        if (count($errors) === 0) {

            $user = selectOne('user', ['email' => $_POST['email']]);

            if (!$user) {
                array_push($errors, 'Email address does not exist');
            } elseif (password_verify($_POST['password'], $user['password'])) {
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
    function Register()
    {
        guestsOnly();
        $table = 'user';
        $res['status'] = 0;
        $errors = validateRegister($_POST);
        if (count($errors) === 0) {
            unset($_POST['re_pass']);

            $_POST['role'] = '0';
            $_POST['name'] = $_POST['firstName'] . ' ' . $_POST['lastName'];
            $_POST['avatar'] = 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg';
            $_POST['createdAt'] = currentTime();
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user_id = create($table, $_POST);
            $user = selectOne($table, ['email' => $_POST['email']]);
            $res['status'] = 1;
            $res['msg'] = 'Register success';
            dd($res);
            exit();
        } else $res['errors'] = $errors;


        dd($res);
        exit();
    }
}