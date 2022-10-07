<?php
require './database/db.php';
require './helper/middleware.php';
class Gallery
{
    public static function addImage()
    {
        checkRequest('POST');
        adminOnly();
        $table = 'gallery';
        $urls = $_POST['gallery'];
        foreach ($urls as $key => $url) :
            $obj['productID'] = $_POST['productID'];
            $obj['URLImage'] =  $url;
            $obj['Sort'] =  $key;
            create($table, $obj);
        endforeach;
        $id = $_POST['productID'];
        update('product', ['ID' => $_POST['productID']], ['updatedAt' => currentTime()]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $res['obj'] = custom("
        SELECT * from gallery where productID = $id
        ");
        dd($res);
        exit();
    }

    public static function deleteImage()
    {
        checkRequest('DELETE');
        $table = 'gallery';
        adminOnly();
        parse_str(file_get_contents("php://input"), $sent_vars);
        $image['ID'] = $sent_vars['ID'];
        $product = selectOne($table, $image);
        if ($product) {

            delete($table, $image);
            $id = $product['productID'];
            update('product', ['ID' => $product['productID']], ['updatedAt' => currentTime()]);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            $res['obj'] = custom("
            SELECT * from gallery where productID = $id
            ");
            dd($res);
            exit();
        }
        $res['status'] = 0;
        $res['errors'] = 'Not found image by ID';
        dd($res);
        exit();
    }
    public static function updateGallery()
    {
        checkRequest('PUT');
        $table = 'gallery';
        adminOnly();
        parse_str(file_get_contents("php://input"), $sent_vars);
        $image['ID'] =  $sent_vars['ID'];
        unset($sent_vars['ID']);
        update($table, $image, $sent_vars);
        $product = selectOne($table, $image);
        $id = $product['productID'];
        update('product', ['ID' => $product['productID']], ['updatedAt' => currentTime()]);
        $res['obj'] = custom("
            SELECT * from gallery where productID = $id
            ");
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
}