<?php
require './app/models/productModel.php';
require './app/models/wishListModel.php';
require './database/db.php';
require './helper/middleware.php';


class Product extends Controllers
{
    public $model_product;
    public function __construct()
    {
        $this->model_product = $this->model('productModel');
    }

    public function ListProduct()
    {
        checkRequest('GET');
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = isset($sent_vars['page']) ? $sent_vars['page'] : 1;
        $perPage = isset($sent_vars['perPage']) ? $sent_vars['perPage'] : 10;
        $category = isset($sent_vars['category']) ? $sent_vars['category'] : '';
        $sale = isset($sent_vars['sale']) ? $sent_vars['sale'] : '';
        $sortBy = isset($sent_vars['sortBy']) ? $sent_vars['sortBy'] : 'name';
        $sortType = isset($sent_vars['sortType']) ? $sent_vars['sortType'] : 'ASC';
        $name = isset($sent_vars['name']) ? $sent_vars['name'] : '';

        $IsPublic = 1;

        if ($name == 'price') $name = 'curPrice';

        $res = $this->model_product->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        dd($res);
        exit();
    }
    public function AdminListProduct()
    {
        checkRequest('GET');
        adminOnly();
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
    public function getProduct($id)
    {
        checkRequest('GET');
        $res = $this->model_product->getDetail($id, '1');
        dd($res);
        exit();
    }
    public function AdminGetProduct($id)
    {
        checkRequest('GET');
        adminOnly();
        $res = $this->model_product->getDetail($id, '');
        dd($res);
        exit();
    }

    public function createProduct()
    {
        checkRequest('POST');
        adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $res = $this->model_product->create($sent_vars);
        dd($res);
        exit();
    }

    public function deleteProduct($id)
    {
        checkRequest('DELETE');
        adminOnly();
        $res = $this->model_product->delete($id);
        dd($res);
        exit();
    }
    public function updateProduct($id)
    {
        checkRequest('PUT');
        $table = 'product';
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $res = $this->model_product->update($id, $sent_vars);
        dd($res);
        exit();
    }
}