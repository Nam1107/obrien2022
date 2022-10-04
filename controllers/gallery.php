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
        $galleryID = create($table, $_POST);
        if (!$galleryID) {
            $res['status'] = 0;
            $res['msg'] = 'Errors';
        } else {
            $product = selectOne($table, ['ID' => $galleryID]);
            update('product', ['ID' => $product['ID']], ['updatedAt' => currentTime()]);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            $res['obj'] = selectOne($table, ['ID' => $galleryID]);
        }

        dd($res);
        exit();
    }

    public static function deleteImage()
    {
        checkRequest('DELETE');
        $table = 'gallery';
        adminOnly();
        parse_str(file_get_contents("php://input"), $sent_vars);
        if (isset($sent_vars['ID'])) {
            $id['ID'] = $sent_vars['ID'];
            $product = selectOne($table, $id);

            $status = delete($table, $id);
            if ($status == 1) {
                update('product', ['ID' => $product['ID']], ['updatedAt' => currentTime()]);
                $res['status'] = 1;
                $res['msg'] = 'Success';
                dd($res);
                exit();
            }
            $res['status'] = 0;
            $res['msg'] = 'Not found image by ID';

            dd($res);
            exit();
        }
    }
    public static function updateGallery()
    {
        checkRequest('PUT');
        $table = 'gallery';
        adminOnly();
        parse_str(file_get_contents("php://input"), $sent_vars);
        $id['ID'] =  $sent_vars['ID'];
        $sent_vars['updatedAt'] = currentTime();
        update($table, $id, ['URLImage' => $sent_vars['URLImage']]);
        $product = selectOne($table, $id);
        update('product', ['ID' => $product['ID']], ['updatedAt' => currentTime()]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
}