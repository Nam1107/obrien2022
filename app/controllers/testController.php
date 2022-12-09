<?php
class testController extends Controllers
{
    public $test_model;
    public $render_view;
    public function __construct()
    {
        $this->test_model = $this->model('testModel');
        $this->render_view = $this->render('renderView');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    function test()
    {
        $test = new testModel();
        $test->test(1, 2);
        $test->ToView();
        // $this->render_view->loadErrors(400, 'You do not have permission to access');
        exit;
    }
}