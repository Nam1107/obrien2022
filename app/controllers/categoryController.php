<?php

class categoryController extends Controllers
{
    public $middle_ware;
    public $category_model;
    public function __construct()
    {
        $this->category_model = $this->model('categoryModel');
        $this->middle_ware = new middleware();
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function listCategory()
    {
        $this->middle_ware->checkRequest('GET');
        $res = $this->category_model->getList();
        $this->ToView($res);
        exit();
    }
    public function addCategory()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $name = $sent_vars['name'];
            $desc = $sent_vars['description'];
            $condition = [
                'name' => $name,
                'description' => $desc,
            ];
            create('category', $condition);
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }


        $res['msg'] = 'Success';
        $this->ToView($res);
        exit();
    }
    public function updateCategory($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        try {
            $name = $sent_vars['name'];
            $desc = $sent_vars['description'];
            $input = [
                'name' => $name,
                'description' => $desc
            ];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res['msg'] = 'Success';
        update('category', ['ID' => $id], $input);
        $this->ToView($res);
        exit();
    }
}
