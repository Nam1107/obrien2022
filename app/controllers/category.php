<?php

class category extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $category_model;
    public function __construct()
    {
        $this->category_model = $this->model('categoryModel');
        $this->middle_ware = new middleware();
    }
    public function listCategory()
    {
        $this->middle_ware->checkRequest('GET');
        $res = $this->category_model->getList();
        dd($res);
        exit();
    }
    public function addCategory()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        if (empty($sent_vars['name'])) {
            $this->loadErrors(400, 'Not enough value');
        }

        $name = $sent_vars['name'];
        $desc = $sent_vars['description'];

        $res = $this->category_model->create($name, $desc);
        dd($res);
        exit();
    }
    public function updateCategory($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        update('category', ['ID' => $id], $sent_vars);
        dd($res);
        exit();
    }
}