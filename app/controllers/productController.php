<?php

class ProductController extends Controllers
{
    public $product_model;
    public $category_model;
    public $middle_ware;
    public function __construct()
    {

        $this->middle_ware = new middleware();
        $this->product_model = $this->model('productModel');
        $this->category_model = $this->model('categoryModel');
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

        $this->ToView($res);
        exit();
    }
    public function AdminListProduct()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();

        $sent_vars = $_GET;


        try {
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
            $category = $sent_vars['category'];
            $sale = $sent_vars['sale'];
            $sortBy = $sent_vars['sortBy'];
            $sortType = $sent_vars['sortType'];
            $name = $sent_vars['name'];
            $IsPublic = '';
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->product_model->getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy,  $sortType);

        $this->ToView($res);
        exit();
    }

    public function getProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->checkProduct($id, 1);
        $res = $this->product_model->getDetail($id, '1');
        $this->ToView($res);
        exit();
    }
    public function AdminGetProduct($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $this->checkProduct($id, '');
        $res = $this->product_model->getDetail($id, '');
        $this->ToView($res);
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
            $check = $this->validateDate($endSale);
            if (!$check) {
                $this->loadErrors(404, 'Errors: day value invalid');
            }

            if ($startSale >= $endSale) {
                $this->loadErrors(404, 'Errors: startSale must less than endSale');
            }

            $input = [
                'priceSale' => $priceSale,
                'startSale' => $startSale,
                'endSale' => $endSale,
                'updatedAt' => currentTime()
            ];
            update('product', ['ID' => $id], $input);

            $res['msg'] = 'Success';
            $this->ToView($res);
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
            $gallery = $sent_vars['gallery'];
            $category = $this->category_model->getDetail($sent_vars['category']);

            if (empty($category)) {
                $this->loadErrors(404, 'Value Category invalid');
            }

            $product = [
                'categoryID' => $category['ID'],
                'name' =>  $sent_vars['name'],
                'price' => $sent_vars['price'],
                'image' => $sent_vars['image'],
                'description' => $sent_vars['description'],
                'IsPublic' => $sent_vars['IsPublic'],
                'createdAt' => currentTime(),
                'updatedAt' => currentTime(),
                'stock' => 0
            ];

            $productID = create('product', $product);
            if ($productID == 0) {
                $this->loadErrors(400, 'Errors: value invalid');
            }

            foreach ($gallery as $key => $url) :
                $image['productID'] = $productID;
                $image['URLImage'] =  $url;
                create('gallery', $image);
            endforeach;
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res['product']['ID'] = $productID;
        $this->ToView($res);
        exit();
    }

    public function deleteProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();
        $this->checkProduct($id);

        delete('product', ['ID' => $id]);

        $res['msg'] = 'Success';
        $this->ToView($res);
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
            $category = $sent_vars['category'];
            $categoryID = custom("SELECT ID FROM category WHERE name like '%$category%'");
            if (!$categoryID) {
                $this->loadErrors(400, "Value Category invalid");
            } else {
                $input['categoryID'] = $categoryID[0]['ID'];
            }
            $input['name'] = $sent_vars['name'];
            $input['price'] = $sent_vars['price'];
            $input['image'] = $sent_vars['image'];
            $input['description'] = $sent_vars['description'];
            $input['IsPublic'] = $sent_vars['IsPublic'];
            $input['updatedAt'] = currentTime();

            update('product', ['ID' => $id], $input);
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }


        $res['msg'] = 'Success';
        $this->ToView($res);
        exit();
    }

    function listStockInput()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $sent_vars = $_GET;


        try {
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];

            $offset = $perPage * ($page - 1);
            $total = custom(
                "SELECT COUNT(ID) as total
                FROM (
                    SELECT ID
                    FROM stock_input
                    WHERE createdAt > '$startDate' AND  createdAt < '$endDate'
                ) AS B
            "
            );

            $check = ceil($total[0]['total'] / $perPage);
            $report = custom("
            SELECT * 
            FROM stock_input
            WHERE createdAt > '$startDate' AND createdAt < '$endDate'
            LIMIT $perPage  OFFSET $offset 
            ");
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = $page;
        $res['obj'] = $report;
        $this->ToView($res);
        exit;
    }

    function stockInput($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $product = $this->checkProduct($id);

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $userID = $_SESSION['user']['ID'];

        try {

            if ($sent_vars['quantity'] <= 0) {
                $this->loadErrors(400, "Error: quantity invalid");
            }
            $quantity = $sent_vars['quantity'] + $product['stock'];
            $input = [
                'quantity' => $sent_vars['quantity'],
                'description' => $sent_vars['description'],
                'createdAt' => currentTime(),
                'productID' => $id,
                'userID' => $userID
            ];
            create('stock_input', $input);
            update('product', ['ID' => $id], ['stock' => $quantity]);
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res['msg'] = 'Success';
        $this->ToView($res);
        exit;
    }
    function stockExpiry($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $product = $this->checkProduct($id);
        $userID = $_SESSION['user']['ID'];

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {

            if ($sent_vars['quantity'] <= 0) {
                $this->loadErrors(400, "Error: quantity invalid");
            }
            $quantity = $product['stock'] - $sent_vars['quantity'];
            if ($quantity < 0) {
                $this->loadErrors(400, "Error: quantity invalid");
            }
            $input = [
                'quantity' => $sent_vars['quantity'],
                'description' => $sent_vars['description'],
                'createdAt' => currentTime(),
                'productID' => $id,
                'userID' => $userID
            ];
            create('stock_expiry', $input);
            update('product', ['ID' => $id], ['stock' => $quantity]);
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res['msg'] = 'Success';
        $this->ToView($res);
        exit;
    }
    function listStockExpiry()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $sent_vars = $_GET;


        try {
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];

            $offset = $perPage * ($page - 1);
            $total = custom(
                "SELECT COUNT(ID) as total
                FROM (
                    SELECT ID
                    FROM stock_expiry
                    WHERE createdAt > '$startDate' AND  createdAt < '$endDate'
                ) AS B
            "
            );

            $check = ceil($total[0]['total'] / $perPage);
            $report = custom("
            SELECT * 
            FROM stock_expiry
            WHERE createdAt > '$startDate' AND createdAt < '$endDate'
            LIMIT $perPage  OFFSET $offset 
            ");
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = $page;
        $res['obj'] = $report;
        $this->ToView($res);
        exit;
    }
}