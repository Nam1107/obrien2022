<?php

function validateRegister($user)
{

    $errors = array();
    $existingUser = selectOne('user', ['email' => $user['email']]);
    if (isset($existingUser)) {
        array_push($errors, 'Email is already registered ');
    }

    return $errors;
}

function validateChangePass($user)
{

    $errors = array();
    if (!password_verify($_POST['password'], $_SESSION['user']['password'])) {
        array_push($errors, 'Wrong password');
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