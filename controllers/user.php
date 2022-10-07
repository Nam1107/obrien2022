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

        $res['status'] = 1;
        if (!isset($_GET['page']) || $_GET['page'] <= 0) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $perPage = 10;
        if (isset($_GET['perPage'])) {
            $perPage = $_GET['perPage'];
        }

        $search = '';
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        }

        $searchValue = 'name';
        if (isset($_GET['searchValue'])) {
            $searchValue = $_GET['searchValue'];
        }

        $sortBy = 'name';
        if (isset($_GET['sortBy'])) {
            $sortBy = $_GET['sortBy'];
        }

        $sortType = 'ASC';
        if (isset($_GET['sortType'])) {
            $sortType = $_GET['sortType'];
        }

        $condition = [
            "$searchValue" => $search,
        ];

        $offset = $perPage * ($page - 1);

        $total = count(selectAll($table, $condition, " ORDER BY $sortBy $sortType "));
        $check = ceil($total / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        $obj = selectAll($table, $condition, " ORDER BY $sortBy $sortType LIMIT $perPage OFFSET $offset");
        $totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");
        $res['obj'] = $obj;
        $res['totalCount'] = $totalCount[0]['totalCount'];
        $res['numOfPage'] = ceil($check);
        $res['page'] = $page;

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

    public static function getUser()
    {
        checkRequest('GET');
        adminOnly();
        $table = 'user';

        $obj = selectOne($table, ['ID' => $_GET['ID']]);
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

    public static function deleteUser()
    {
        checkRequest('DELETE');
        adminOnly();
        $table = 'user';
        parse_str(file_get_contents("php://input"), $sent_vars);

        if (isset($sent_vars['ID'])) {
            if ($sent_vars['ID'] == $_SESSION['user']['ID']) {
                $res['status'] = 0;
                $res['errors'] = 'You cannot delete your account';
            } else {

                $id['ID'] = $sent_vars['ID'];
                delete($table, $id);
                $res['status'] = 1;
                $res['msg'] = 'Success';
            }
        } else {
            $res['status'] = 0;
            $res['errors'] = 'Not found user by ID';
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
        parse_str(file_get_contents("php://input"), $sent_vars);
        $id['ID'] = $_SESSION['user']['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID'],  $sent_vars['role']);
        $res['msg'] = update($table, $id, $sent_vars);
        $res['obj'] = selectOne($table, $id);
        dd($res);
        exit();
    }

    public static function updateUser()
    {
        checkRequest('PUT');
        adminOnly();
        $table = 'user';
        $res['status'] = 1;
        parse_str(file_get_contents("php://input"), $sent_vars);
        $id['ID'] =  $sent_vars['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID']);
        update($table, $id, $sent_vars);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public static function setPassword()
    {
        checkRequest('POST');
        adminOnly();
        $table = 'user';

        $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user['ID'] = $_POST['ID'];
        $var['updatedAt'] = currentTime();
        $var['password'] = $_POST['password'];
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

        $errors = validateChangePass($_POST);
        if (count($errors) === 0) {
            $_POST['newPass'] = password_hash($_POST['newPass'], PASSWORD_DEFAULT);
            $user['ID'] = $_SESSION['user']['ID'];
            unset($_POST['confirmPass'], $_POST['ID']);
            $var['updatedAt'] = currentTime();
            $var['password'] = $_POST['newPass'];
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