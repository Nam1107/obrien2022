<?php

class Product extends Controllers
{
    public $product_model;
    public $middle_ware;
    public function __construct()
    {

        $this->middle_ware = new middleware();
        $this->product_model = $this->model('productModel');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function checkProduct($id, $IsPublic = '')
    {
        // $pro = selectOne('product', ['ID' => $id]);
        $product = custom("SELECT * FROM product WHERE ID = $id AND IsPublic like '%$IsPublic%'");
        if (!$product) {
            $this->loadErrors(404, 'Not found product');
            exit();
        }
        return $product[0];
    }
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d;
    }
    public function ListProduct()
    {
        $this->middle_ware->checkRequest('GET');
        $res['status'] = 1;

        $sent_vars = $_GET;

        try {
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
            $category = $sent_vars['category'];
            $sale = $sent_vars['sale'];
            $sortBy = $sent_vars['sortBy'];
            $sortType = $sent_vars['sortType'];
            $name = $sent_vars['name'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $IsPublic = 1;

        if ($name == 'price') $name = 'curPrice';

        $res = $this->product_model->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        dd($res);
        exit();
    }
    public function AdminListProduct()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res['status'] = 1;
        // $json = file_get_contents("php://input");
        // $sent_vars = json_decode($json, TRUE);

        $sent_vars = $_GET;


        try {
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
            $category = $sent_vars['category'];
            $sale = $sent_vars['sale'];
            $sortBy = $sent_vars['sortBy'];
            $sortType = $sent_vars['sortType'];
            $name = $sent_vars['name'];
            if ($name == 'price') $name = 'curPrice';
            $IsPublic = '';
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->product_model->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        dd($res);
        exit();
    }
    public function getProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->checkProduct($id, 1);
        $res = $this->product_model->getDetail($id, '1');
        dd($res);
        exit();
    }
    public function AdminGetProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $this->checkProduct($id, '');
        $res = $this->product_model->getDetail($id, '');
        dd($res);
        exit();
    }

    function updateSale($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $product = $this->checkProduct($id, '');

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $priceSale = $sent_vars['priceSale'];
            if ($priceSale < 0 || $priceSale > $product['price']) {
                $this->loadErrors(404, 'Errors: price-sale invalid');
            }
            if ($priceSale < $product['price'] / 2) {
                $this->loadErrors(404, 'Errors: price-sale must not less than half price');
            }
            $startSale = $sent_vars['startSale'];
            $endSale = $sent_vars['endSale'];
            $check = $this->validateDate($startSale);
            if (!$check) {
                $this->loadErrors(404, 'Errors: day value invalid');
            }
            $check = var_dump($this->validateDate($endSale));
            if (!$check) {
                $this->loadErrors(404, 'Errors: day value invalid');
            }

            $input = [
                'priceSale' => $priceSale,
                'startSale' => $startSale,
                'endSale' => $endSale
            ];
            $res = $this->product_model->update($id, $input);
            dd($res);
            exit();
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }

    public function createProduct()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $cate = $sent_vars['category'];
            $gallery = $sent_vars['gallery'];
            $category = custom("
                SELECT * FROM category WHERE name LIKE '%$cate%' 
            ");

            if (empty($category)) {
                $this->loadErrors(404, 'Not found category');
            }

            $product = [
                'categoryID' => $category[0]['ID'],
                'name' =>  $sent_vars['name'],
                'price' => $sent_vars['price'],
                'image' => $sent_vars['image'],
                'description' => $sent_vars['description'],
                'IsPublic' => $sent_vars['IsPublic']
            ];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->product_model->create($product, $gallery);
        dd($res);
        exit();
    }

    public function deleteProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();
        $this->checkProduct($id);
        $res = $this->product_model->delete($id);
        dd($res);
        exit();
    }
    public function updateProduct($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $this->checkProduct($id);
        try {
            $input['name'] = $sent_vars['name'];
            $input['price'] = $sent_vars['price'];
            $input['image'] = $sent_vars['image'];
            $input['description'] = $sent_vars['description'];
            $input['IsPublic'] = $sent_vars['IsPublic'];

            $category = $sent_vars['category'];
            $categoryID = custom("SELECT ID FROM category WHERE name like '%$category%'");
            if (!$categoryID) {
                $this->loadErrors(400, "Error: Not found category");
            } else {
                $input['categoryID'] = $categoryID[0]['ID'];
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res = $this->product_model->update($id, $input);
        dd($res);
        exit();
    }
}