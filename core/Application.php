<?php

// require './database/db.php';
// require './helper/middleware.php';
class Application
{
    protected $controller;
    protected $action;
    protected $prarams = [];

    function __construct()
    {

        $arr = $this->UrlProcess();
        array_splice($arr, 0, 1);
        // switch($arr[0]){
        //     case 'product':
        //         break;
        //     case 'test':
        //         break;
        // }
        // if ($arr[0] == 'product') {
        //     require_once './controllers/product.php';
        //     if ($arr[1] == 'listproduct') {
        //         // call_user_func('ListProduct', '');
        //         // test();
        //         ListProduct();
        //     } elseif ($arr[1] == 'getProduct') {
        //         getProduct();
        //     } elseif ($arr[1] == 'test') {
        //         test();
        //     }
        // if ($arr[1] == 'createProduct') {
        //     createProduct();
        // }
        // if ($arr[1] == 'getProduct') {
        //     getProduct()
        // }
        // if ($arr[1] == 'getProduct') {
        //     getProduct()
        // }
        // if ($arr[1] == 'getProduct') {
        //     getProduct()
        // }
        // }



        if (file_exists("./controllers/" . $arr[0] . ".php")) {
            $this->controller = $arr[0];
            require "./controllers/" . $arr[0] . ".php";
            if (isset($arr[1])) {
                if (method_exists($this->controller, $arr[1])) {
                    $this->action = $arr[1];
                }
                call_user_func([$this->controller, $this->action]);
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