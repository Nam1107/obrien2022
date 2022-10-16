<?php

class Application
{
    protected $controller;
    protected $action;
    protected $prarams;

    function __construct()
    {

        $arr = $this->UrlProcess();
        array_splice($arr, 0, 1);
        if (file_exists("./controllers/" . $arr[0] . ".php")) {
            $this->controller = $arr[0];
            require "./controllers/" . $arr[0] . ".php";
            if (isset($arr[1])) {

                if (method_exists($this->controller, $arr[1])) {
                    $this->action = $arr[1];
                } else {
                    $val['status'] = 0;
                    $val['errors'] = "Not found '$arr[1]'";
                    echo json_encode($val);
                    exit();
                }
                if (isset($arr[2])) $this->prarams = $arr[2];
                call_user_func([$this->controller, $this->action], $this->prarams);
            }
        }
    }

    function UrlProcess()
    {
        $str = explode('?', $_SERVER['REQUEST_URI']);
        $resStr = strtolower($str[0]);
        $req = filter_var(str_replace('/php/obrien', '', $resStr));

        return explode('/', $req);
    }
}