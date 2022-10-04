<?php

require './database/db.php';
require './helper/middleware.php';
class Application
{
    protected $controller;
    protected $action;
    protected $prarams = [];

    function __construct()
    {

        $arr = $this->UrlProcess();
        array_splice($arr, 0, 1);

        // // print_r($arr);
        if ($arr[0] == 'product') {
            require_once './controllers/product.php';
            if ($arr[1] == 'listproduct') {
                test();
            }
        }



        // if (file_exists("./controllers/" . $arr[0] . ".php")) {
        //     $this->controller = $arr[0];
        //     require_once "./controllers/" . $arr[0] . ".php";
        //     if (isset($arr[1])) {
        //         if (method_exists($this->controller, $arr[1])) {
        //             $this->action = $arr[1];
        //         }
        //         call_user_func_array([$this->controller, $this->action], []);
        //     }
        // }
    }

    function UrlProcess()
    {
        $str = explode('?', $_SERVER['REQUEST_URI']);
        $resStr = strtolower($str[0]);
        $req = filter_var(str_replace('/php/obrien', '', $resStr));

        return explode('/', $req);
    }
}