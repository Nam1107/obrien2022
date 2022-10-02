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

        // $arr = $this->UrlProcess();

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
        $req = explode('?', filter_var(str_replace('/PHP/obrien/', '', $_SERVER['REQUEST_URI'])));
        return explode('/', $req[0]);
    }
}