<?php
require "./core/Controllers.php";
class Application
{
    protected $controller;
    protected $action;
    protected $prarams;

    public function __construct()
    {

        $arr = $this->UrlProcess();
        array_splice($arr, 0, 1);
        if (file_exists("./app/controllers/" . $arr[0] . ".php")) {
            $this->controller = $arr[0];
            require "./app/controllers/" . $arr[0] . ".php";
            $this->controller = new $this->controller();
            if (isset($arr[1])) {

                if (method_exists($this->controller, $arr[1])) {
                    $this->action = $arr[1];
                } else {
                    $this->loadError($arr[1]);
                }
                if (isset($arr[2])) $this->prarams = $arr[2];
                call_user_func([$this->controller, $this->action], $this->prarams);
            }
        } else {
            $this->loadError($arr[0]);
        }
    }

    public function UrlProcess()
    {
        $str = explode('?', $_SERVER['REQUEST_URI']);
        $resStr = strtolower($str[0]);
        $req = filter_var(str_replace('/php/obrien', '', $resStr));

        return explode('/', $req);
    }
    public function loadError($error)
    {
        $val['status'] = 0;
        $val['errors'] = "Not found '$error'";
        echo json_encode($val);
        exit();
    }
}