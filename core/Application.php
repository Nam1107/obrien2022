<?php

// require_once 'Router.php';

class Application
{
    protected $controller;
    protected $action;
    protected $prarams = [];

    function __construct()
    {
        print_r($_SERVER['REQUEST_URI']);

        $arr = $this->UrlProcess();

        print_r($arr);

        // if (file_exists("../controllers/" . $arr[0] . ".php")) {
        //     $this->controller = $arr[0];
        //     require_once "../controllers/" . $arr[0] . ".php";
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
        $req = filter_var(str_replace('/php/obrien/', '', $resStr));
        return explode('/', $req);
    }
}