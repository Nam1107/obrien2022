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
}