<?php
require './database/db.php';
require './helper/middleware.php';
require './helper/validateUser.php';
class User
{
    function ListUser()
    {
        checkRequest('GET');
        adminOnly();
        $table = 'user';
        // $res['status'] = 0; // 1: success; 0: failed;

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

        $searchType = 'name';
        if (isset($_GET['searchType'])) {
            $searchType = $_GET['searchType'];
        }

        $orderBy = 'name';
        if (isset($_GET['orderBy'])) {
            $orderBy = $_GET['orderBy'];
        }

        $orderType = 'ASC';
        if (isset($_GET['orderType'])) {
            $orderType = $_GET['orderType'];
        }

        $condition = [
            "$searchType" => $search,
        ];

        $offset = $perPage * ($page - 1);

        $total = count(selectAll($table, $condition, " ORDER BY $orderBy $orderType "));
        $check = ceil($total / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        $obj = selectAll($table, $condition, " ORDER BY $orderBy $orderType LIMIT $perPage OFFSET $offset");
        $totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");
        $res['obj'] = $obj;
        $res['totalCount'] = $totalCount[0]['totalCount'];
        $res['numOfPage'] = ceil($check);
        $res['page'] = $page;

        dd($res);
        exit();
    }

    function getProfile()
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

    function getUser()
    {
        checkRequest('GET');
        adminOnly();
        $table = 'user';
        $res['status'] = 0; // 1: success; 0: failed;
        $res['status'] = 1;
        $obj = selectOne($table, ['ID' => $_GET['ID']]);
        $res['obj'] = $obj;
        dd($res);
        exit();
    }

    function deleteUser()
    {
        checkRequest('DELETE');
        adminOnly();
        $table = 'user';
        parse_str(file_get_contents("php://input"), $sent_vars);

        if (isset($sent_vars['ID'])) {
            if ($sent_vars['ID'] == $_SESSION['user']['ID']) {
                $res['status'] = 0;
                $res['msg'] = 'You cannot delete your account';
            } else {

                $id['ID'] = $sent_vars['ID'];
                delete($table, $id);
                $res['status'] = 1;
                $res['msg'] = delete($table, $id);
            }
        } else {
            $res['status'] = 0;
            $res['msg'] = 'Not found user by ID';
        }
        dd($res);
        exit();
    }

    function updateProfile()
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
        dd($res);
        exit();
    }

    function updateUser()
    {
        checkRequest('PUT');
        adminOnly();
        $table = 'user';
        $res['status'] = 1;
        parse_str(file_get_contents("php://input"), $sent_vars);
        $id['ID'] =  $sent_vars['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID']);
        $res['msg'] = update($table, $id, $sent_vars);
        dd($res);
        exit();
    }

    function setPassword()
    {
        checkRequest('POST');
        adminOnly();
        $table = 'user';
        $res['status'] = 0;
        $errors = validateChangePass($_POST);
        if (count($errors) === 0) {
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user['ID'] = $_POST['ID'];
            unset($_POST['re_pass'], $_POST['ID']);
            $sent_vars['updatedAt'] = currentTime();
            $res['rep'] = 1;
            $res['msg'] = update('user', $user, $_POST);
            dd($res);
            exit();
        } else {
            $res['rep'] = 0;
            $res['msg'] = $errors;
            dd($res);
            exit();
        }
    }

    function changePassword()
    {
        checkRequest('POST');
        userOnly();
        $table = 'user';

        $errors = validateChangePass($_POST);
        if (count($errors) === 0) {
            $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $user['ID'] = $_SESSION['user']['ID'];
            unset($_POST['re_pass'], $_POST['ID']);
            $sent_vars['updatedAt'] = currentTime();

            $res['status'] = 1;
            $res['msg'] = update('user', $user, $_POST);
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