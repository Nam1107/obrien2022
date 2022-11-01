<?php

class Product extends Controllers
{
    public $model_product;
    public $middle_ware;
    public function __construct()
    {

        $this->middle_ware = new middleware();
        $this->model_product = $this->model('productModel');
    }

    public function ListProduct()
    {
        $this->middle_ware->checkRequest('GET');
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = !empty($sent_vars['page']) ? $sent_vars['page'] : 1;
        $perPage = !empty($sent_vars['perPage']) ? $sent_vars['perPage'] : 10;
        $category = !empty($sent_vars['category']) ? $sent_vars['category'] : '';
        $sale = !empty($sent_vars['sale']) ? $sent_vars['sale'] : '';
        $sortBy = !empty($sent_vars['sortBy']) ? $sent_vars['sortBy'] : 'name';
        $sortType = !empty($sent_vars['sortType']) ? $sent_vars['sortType'] : 'ASC';
        $name = !empty($sent_vars['name']) ? $sent_vars['name'] : '';

        $IsPublic = 1;

        if ($name == 'price') $name = 'curPrice';

        $res = $this->model_product->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        dd($res);
        exit();
    }
    public function AdminListProduct()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = isset($sent_vars['page']) ? $sent_vars['page'] : 1;
        $perPage = isset($sent_vars['perPage']) ? $sent_vars['perPage'] : 10;
        $category = isset($sent_vars['category']) ? $sent_vars['category'] : '';
        $sale = isset($sent_vars['sale']) ? $sent_vars['sale'] : '';
        $sortBy = isset($sent_vars['sortBy']) ? $sent_vars['sortBy'] : 'name';
        $sortType = isset($sent_vars['sortType']) ? $sent_vars['sortType'] : 'ASC';

        $IsPublic = '';

        $name = isset($sent_vars['name']) ? $sent_vars['name'] : 'name';

        if ($name == 'price') $name = 'curPrice';
        $res = $this->model_product->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        dd($res);
        exit();
    }
    public function getProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $res = $this->model_product->getDetail($id, '1');
        dd($res);
        exit();
    }
    public function AdminGetProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res = $this->model_product->getDetail($id, '');
        dd($res);
        exit();
    }

    public function createProduct()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $res = $this->model_product->create($sent_vars);
        dd($res);
        exit();
    }

    public function deleteProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();
        $res = $this->model_product->delete($id);
        dd($res);
        exit();
    }
    public function updateProduct($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $res = $this->model_product->update($id, $sent_vars);
        dd($res);
        exit();
    }
}