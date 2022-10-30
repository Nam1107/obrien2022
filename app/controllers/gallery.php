<?php
require './database/db.php';
require './helper/middleware.php';
class Gallery
{
    public static function addImage($id)
    {
        checkRequest('POST');
        adminOnly();
        $table = 'gallery';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $urls = $sent_vars['gallery'];
        foreach ($urls as $key => $url) :
            $obj['productID'] = $id;
            $obj['URLImage'] =  $url;
            $obj['Sort'] =  $key;
            create($table, $obj);
        endforeach;
        update('product', ['ID' => $id], ['updatedAt' => currentTime()]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public static function deleteImage($id)
    {
        checkRequest('DELETE');
        $table = 'gallery';
        adminOnly();
        $image['ID'] = $id;
        $product = selectOne($table, $image);
        if ($product) {
            delete($table, $image);
            $productID = $product['productID'];
            update('product', ['ID' => $product['productID']], ['updatedAt' => currentTime()]);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            $res['obj'] = custom("
            SELECT * from gallery where productID = $productID
            ");
            dd($res);
            exit();
        }
        $res['status'] = 0;
        $res['errors'] = 'Not found image by ID';
        dd($res);
        exit();
    }
    public static function updateGallery($id)
    {
        checkRequest('PUT');
        $table = 'gallery';
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $image['ID'] =  $id;
        update($table, $image, $sent_vars);
        $product = selectOne($table, $image);
        $productID = $product['productID'];
        update('product', ['ID' => $product['productID']], ['updatedAt' => currentTime()]);
        $res['obj'] = custom("
            SELECT * from gallery where productID = $productID
            ");
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
}