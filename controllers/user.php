<?php
require './database/db.php';

class User
{


    function ListUser()
    {
        $table = 'user';
        $res['status'] = 0; // 1: success; 0: failed;
        if (!isset($_GET['per-page'])) {
            dd($res);
            exit();
        }
        $res['status'] = 1;
        if (!isset($_GET['page']) || $_GET['page'] <= 0) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }
        $orderBy = 'ASC';
        if (isset($_GET['order-by'])) {
            $orderBy = $_GET['order-by'];
        }
        $perPage = $_GET['per-page'];
        // $perPage = 2;
        $offset = $perPage * ($page - 1);

        $total = count(search($table, [], " ORDER BY createdAt $orderBy"));
        $check = ceil($total / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        $obj = search($table, [], " ORDER BY createdAt $orderBy LIMIT $perPage OFFSET $offset");
        $TotalCount = custom("SELECT COUNT(*)  AS TotalCount FROM $table");
        $res['Obj'] = $obj;
        $res['TotalCount'] = $TotalCount[0]['TotalCount'];
        $res['NumOfPage'] = ceil($check);
        $res['Page'] = $page;

        dd($res);
        exit();
    }
    function getUser()
    {
        $table = 'user';
        $res['status'] = 0; // 1: success; 0: failed;
        if (!isset($_GET['action'])) {

            dd($res);
            exit();
        }
        if ($_GET['action'] == 'getUser') {
            $res['status'] = 1;
            $obj = selectOne($table, ['ID' => $_GET['ID']]);
            $res['Obj'] = $obj;
        }

        dd($res);
        exit();
    }

    function deleteUser()
    {
        $table = 'user';
        $res['status'] = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $sent_vars);
            if (isset($sent_vars['ID'])) {
                $id['ID'] = $sent_vars['ID'];
                delete($table, $id);
                $res['status'] = 1;
            }
        }
        dd($res);
        exit();
    }
    function updateProfile()
    {
        $table = 'user';
        $res['status'] = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            parse_str(file_get_contents("php://input"), $sent_vars);
            $id['ID'] =  $sent_vars['ID'];
            unset($sent_vars['email'], $sent_vars['password'],  $sent_vars['role']);
            $res['msg'] = update($table, $id, $sent_vars);
            dd($res);
            exit();
        }
    }
}