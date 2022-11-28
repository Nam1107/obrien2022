<?php
class sliderController extends Controllers
{
    public $middle_ware;
    public $user_model;
    public $render_view;
    public function __construct()
    {
        $this->user_model = $this->model('sliderModel');
        $this->middle_ware = new middleware();
        $this->render_view = $this->render('renderView');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function listslider()
    {
        $this->middle_ware->checkRequest('GET');
        $obj = custom("
            SELECT * from slider WHERE IsPublic = 1
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public function adminlistslider()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $obj = custom("
            SELECT * from slider
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public function addslider()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        try {
            $condition = [
                'description' => $sent_vars['description'],
                'URLImage' => $sent_vars['URLImage'],
                'URLPage' => $sent_vars['URLPage'],
                'sort' => $sent_vars['sort'],
            ];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        create('slider', $condition);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
    public function updateslider($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        try {
            $condition = [
                'description' => $sent_vars['description'],
                'URLImage' => $sent_vars['URLImage'],
                'URLPage' => $sent_vars['URLPage'],
                'sort' => $sent_vars['sort'],
            ];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $check = update('slider', ['ID' => $id], $condition);
        if ($check == 1) {
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $this->loadErrors(404, 'Not found');
        }
    }
    public function deleteSlider($id)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();

        $check = delete('slider', ['ID' => $id]);
        if ($check == 1) {
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $this->loadErrors(404, 'Not found slider');
        }
    }
}