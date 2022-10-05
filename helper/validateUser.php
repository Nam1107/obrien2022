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
    if (empty($user['confirmPass'])) {
        array_push($errors, 'Password Confirm is required');
    } elseif ($user['confirmPass'] !== $user['password']) {
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
    if (!password_verify($_POST['password'], $_SESSION['user']['password'])) {
        array_push($errors, 'Wrong password');
    } elseif (strlen($user['newPass']) < 6) {
        array_push($errors, 'Password must have more than 6 characters');
    } elseif (empty($user['confirmPass'])) {
        array_push($errors, 'Password Confirm is required');
    } elseif ($user['confirmPass'] !== $user['newPass']) {
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