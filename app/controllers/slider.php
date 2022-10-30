<?php
require './database/db.php';
require './helper/middleware.php';

class slider
{
    public static function listslider()
    {
        checkRequest('GET');
        $res['status'] = 1;
        $obj = custom("
            SELECT * from slider WHERE IsPublic = 1
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public static function adminlistslider()
    {
        checkRequest('GET');
        adminOnly();
        $res['status'] = 1;
        $obj = custom("
            SELECT * from slider
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public static function addslider()
    {
        checkRequest('POST');
        adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $res['status'] = 1;
        $condition = [
            'description' => $sent_vars['description'],
            'URLImage' => $sent_vars['URLImage'],
            'URLPage' => $sent_vars['URLPage'],
            'sort' => $sent_vars['sort'],
        ];
        create('slider', $condition);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
    public static function updateslider($id)
    {
        checkRequest('PUT');
        adminOnly();
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $condition = [
            'description' => $sent_vars['description'],
            'URLImage' => $sent_vars['URLImage'],
            'URLPage' => $sent_vars['URLPage'],
            'sort' => $sent_vars['sort'],
        ];
        $check = update('slider', ['ID' => $id], $condition);
        if ($check == 1) {
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['msg'] = 'Not found slider';
            dd($res);
            exit();
        }
    }
    public static function deleteSlider($id)
    {
        checkRequest('DELETE');
        adminOnly();

        $check = delete('slider', ['ID' => $id]);
        if ($check == 1) {
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['msg'] = 'Not found slider';
            dd($res);
            exit();
        }
    }
}