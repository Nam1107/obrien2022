<?php
class Controllers
{
    public function model($model)
    {
        if (file_exists("./app/models/" . $model . ".php")) {
            require_once "./app/models/" . $model . ".php";
            if (class_exists($model)) {
                $model = new $model();
                return $model;
            }
        }
        return false;
    }
    public function loadErrors($code, $errors)
    {
        http_response_code($code);
        $res['status'] = 0;
        $res['errors'] = $errors;
        dd($res);
        exit();
    }
}