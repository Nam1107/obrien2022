<?php
require './database/db.php';
require './helper/middleware.php';
require './helper/validateUser.php';
class User
{
    public static function ListUser()
    {
        checkRequest('GET');
        adminOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $email = $sent_vars['email'];
        $sortBy = $sent_vars['sortBy'];
        $sortType = $sent_vars['sortType'];
        $condition = [
            "email" => $email,
        ];

        $offset = $perPage * ($page - 1);

        // $total = count(selectAll($table, $condition, " ORDER BY $sortBy $sortType "));
        $total = custom("
        SELECT COUNT(ID) as total
        FROM (SELECT * FROM `user` WHERE email LIKE '%$email%' ORDER BY $sortBy $sortType) as B
        ");
        $check = ceil($total[0]['total'] / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        // $obj = selectAll($table, $condition, " ORDER BY $sortBy $sortType LIMIT $perPage OFFSET $offset");

        $obj = custom("
        SELECT * FROM `user` WHERE email LIKE '%$email%' ORDER BY $sortBy $sortType LIMIT $perPage OFFSET $offset
        ");
        $totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");
        $res['totalCount'] = $totalCount[0]['totalCount'];
        $res['numOfPage'] = ceil($check);
        $res['page'] = $page;
        $res['obj'] = $obj;

        dd($res);
        exit();
    }

    public static function getProfile()
    {
        checkRequest('GET');
        userOnly();
        $table = 'user';
        $res['status'] = 1;
        $id = $_SESSION['user']['ID'];
        $obj = selectOne($table, ['ID' => $id]);
        $res['obj'] = $obj;
        dd($res);
        exit();
    }

    public static function getUser($id)
    {
        checkRequest('GET');
        adminOnly();
        $table = 'user';

        $obj = selectOne($table, ['ID' => $id]);
        if (!$obj) {
            $res['status'] = 0;
            $res['errors'] = 'Not found user by ID';
            dd($res);
            exit();
        }
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $res['obj'] = $obj;
        dd($res);
        exit();
    }

    public static function deleteUser($id)
    {
        checkRequest('DELETE');
        adminOnly();
        $table = 'user';
        $json = file_get_contents("php://input");

        $sent_vars = json_decode($json, TRUE);

        if ($id == $_SESSION['user']['ID']) {
            $res['status'] = 0;
            $res['errors'] = 'You cannot delete your account';
        } else {
            $userID['ID'] = $id;
            delete($table, $userID);
            $res['status'] = 1;
            $res['msg'] = 'Success';
        }

        dd($res);
        exit();
    }

    public static function updateProfile()
    {
        checkRequest('PUT');
        userOnly();
        $table = 'user';
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);


        $id['ID'] = $_SESSION['user']['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID'],  $sent_vars['role']);
        $res['msg'] = update($table, $id, $sent_vars);
        $res['obj'] = selectOne($table, $id);
        dd($res);
        exit();
    }

    public static function updateUser($id)
    {
        checkRequest('PUT');
        adminOnly();
        $table = 'user';
        $res['status'] = 1;

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $userID['ID'] =  $id;
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID']);
        update($table, $userID, $sent_vars);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public static function setPassword($id)
    {
        checkRequest('POST');
        adminOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $sent_vars['password'] = password_hash($sent_vars['password'], PASSWORD_DEFAULT);
        $user['ID'] = $id;
        $var['updatedAt'] = currentTime();
        $var['password'] = $sent_vars['password'];
        update('user', $user, $var);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public static function changePassword()
    {
        checkRequest('POST');
        userOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $errors = validateChangePass($sent_vars);
        if (count($errors) === 0) {
            $sent_vars['newPass'] = password_hash($sent_vars['newPass'], PASSWORD_DEFAULT);
            $user['ID'] = $_SESSION['user']['ID'];
            unset($sent_vars['confirmPass'], $sent_vars['ID']);
            $var['updatedAt'] = currentTime();
            $var['password'] = $sent_vars['newPass'];
            update('user', $user, $var);

            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['msg'] = $errors;
            dd($res);
            exit();
        }
    }
}