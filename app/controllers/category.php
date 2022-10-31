<?php

class category extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $wishlist_model;
    public function __construct()
    {
        $this->wishlist_model = $this->model('categoryModel');
        $this->middle_ware = new middleware();
    }
    public function listCategory()
    {
        $this->middle_ware->checkRequest('GET');
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $obj = custom("
            SELECT * from category
        ");
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
    public function addCategory()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $condition = [
            'name' => $sent_vars['name'],
            'description' => $sent_vars['description'],

        ];
        create('category', $condition);
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