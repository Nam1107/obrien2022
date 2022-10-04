<?php

function validateRegister($user)
{

    $errors = array();
    if (empty($user['firstName'])) {
        array_push($errors, 'firstName is required');
    }
    if (empty($user['lastName'])) {
        array_push($errors, 'lastName is required');
    }
    if (empty($user['email'])) {
        array_push($errors, 'Email is required');
    }
    if (strlen($user['password']) < 6) {
        array_push($errors, 'Password must have more than 6 characters');
    }
    if (empty($user['password'])) {
        array_push($errors, 'Password is required');
    }
    if (empty($user['re_pass'])) {
        array_push($errors, 'Password Confirm is required');
    } elseif ($user['re_pass'] !== $user['password']) {
        array_push($errors, 'password do not match');
    }
    $existingUser = selectOne('user', ['email' => $user['email']]);
    if (isset($existingUser)) {
        array_push($errors, 'Email is already registered ');
    }

    return $errors;
}

function validateChangePass($user)
{

    $errors = array();
    if (strlen($user['password']) < 6) {
        array_push($errors, 'Password must have more than 6 characters');
    }
    if (empty($user['re_pass'])) {
        array_push($errors, 'Password Confirm is required');
    } elseif ($user['re_pass'] !== $user['password']) {
        array_push($errors, 'Password do not match');
    }

    return $errors;
}
function validateLogin($user)
{

    $errors = array();

    if (empty($user['email']) || !isset($user['email'])) {
        array_push($errors, 'Email is required');
    }
    if (empty($user['password']) || !isset($user['password'])) {
        array_push($errors, 'Password is required');
    }


    return $errors;
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

    $result   = custom("select [User].* from [User], Login_Token where [User].UserID = [Login_Token].UserID and [Login_Token].token = '$token'");

    if ($result != null && count($result) > 0) {
        $_SESSION['user'] = $result[0];

        return $result[0];
    }

    return null;
}