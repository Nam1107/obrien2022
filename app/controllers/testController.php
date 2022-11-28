<?php
class testController extends Controllers
{
    public $test_model;

    public function __construct()
    {
        $this->test_model = $this->model('testModel');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    function getDetail()
    {
        $test = new testModel();
        $test->test(1, 2);
        $test->ToView();
        exit;
    }
}