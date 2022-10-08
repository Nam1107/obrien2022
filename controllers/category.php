<?php
require './database/db.php';
require './helper/middleware.php';
class category
{
    public static function listCategory()
    {
        checkRequest('GET');
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $obj = custom("
            SELECT * from category
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public static function addCategory()
    {
        checkRequest('POST');
        adminOnly();
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $condition = [
            'name' => $_POST['name'],
            'description' => $_POST['description'],

        ];
        create('category', $condition);
        dd($res);
        exit();
    }
    public static function updateCategory($id)
    {
        checkRequest('PUT');
        adminOnly();
        $res['status'] = 1;
        $res['msg'] = 'Success';
        parse_str(file_get_contents("php://input"), $sent_vars);
        update('category', ['ID' => $id], $sent_vars);
        dd($res);
        exit();
    }
}