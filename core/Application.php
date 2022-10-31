<?php
require "./core/Controllers.php";
class Application
{
    private $controller;
    private $action;
    private $prarams;

    public function __construct()
    {
        global $router;
        $this->controller;
        $this->action;
        $this->prarams = [];
        $this->handleUrl();
    }

    public function handleUrl()
    {
        $arr = $this->UrlProcess();
        if (empty($arr[0]) || empty($arr[1])) {
            $this->loadError(404);
        }

        if (file_exists("./app/controllers/" . $arr[0] . ".php")) {
            $this->controller = $arr[0];
            require "./app/controllers/" . $arr[0] . ".php";
            $this->controller = new $this->controller();
            unset($arr[0]);
        } else {
            $this->loadError(404);
        }

        if (method_exists($this->controller, $arr[1])) {
            $this->action = $arr[1];
            unset($arr[1]);
        } else {
            $this->loadError(404);
        }

        $this->prarams = array_values($arr);
        call_user_func_array([$this->controller, $this->action], $this->prarams);
    }

    public function UrlProcess()
    {
        //getURL
        $str = explode('?', $_SERVER['REQUEST_URI']);
        $strLower = strtolower($str[0]);
        $url = str_replace('/php/obrien', '', $strLower);

        //change URL to array
        $urlArr = array_filter(explode('/', $url));
        $urlArr = array_values($urlArr);
        return $urlArr;
    }
    public function loadError($error)
    {
        $res['status'] = 0;
        $res['errors'] = 'Not found';
        http_response_code($error);
        exit;
    }
}