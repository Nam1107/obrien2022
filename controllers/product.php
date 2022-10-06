<?php
require './database/db.php';
require './helper/middleware.php';

class Product
{

    public static function ListProduct()
    {
        checkRequest('GET');
        $table = 'product';
        $res['status'] = 1;

        if (!isset($_GET['perPage']) || !isset($_GET['category']) || !isset($_GET['name']) || !isset($_GET['sale'])) {
            $res['msg'] = 'Not enough information';
            dd($res);
            exit();
        }

        if ($_GET['page'] <= 0) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }

        $perPage = $_GET['perPage'];
        $category = $_GET['category'];
        $name = $_GET['name'];
        $sale = $_GET['sale'];

        $sortBy = 'name';
        if (isset($_GET['sortBy'])) {
            $sortBy = $_GET['sortBy'];
        }

        $sortType = 'ASC';
        if (isset($_GET['sortType'])) {
            $sortType = $_GET['sortType'];
        }

        $offset = $perPage * ($page - 1);

        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT A.* , category.name AS category
                FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
                FROM product) AS A,category
                WHERE A.categoryID = category.ID
                AND category.name LIKE '%$category%'
                AND A.name LIKE '%$name%'
                AND statusSale LIKE '%$sale%'
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        $obj = custom(
            "SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND category.name LIKE '%$category%'
            AND A.name LIKE '%$name%'
            AND statusSale LIKE '%$sale%'
            ORDER BY $sortBy $sortType
            LIMIT $perPage OFFSET $offset
            "
        );
        $totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");

        $res['obj'] = $obj;
        $res['totalCount'] = $totalCount[0]['totalCount'];
        $res['numOfPage'] = ceil($check);
        $res['page'] = $page;

        dd($res);
        exit();
    }
    public static function getProduct()
    {
        checkRequest('GET');
        $table = 'product';
        $res['status'] = 1;

        $id = $_GET['ID'];

        $obj = custom("
            SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND A.ID = $id
        
        ");

        $res['obj'] = $obj[0];
        dd($res);
        exit();
    }

    public static function createProduct()
    {
        checkRequest('POST');
        adminOnly();
        $table = 'product';
        $res['status'] = 0;
        $_POST['createdAt'] = currentTime();
        $_POST['updatedAt'] = currentTime();
        $productID = create($table, $_POST);
        if (!$productID) {
            $res['status'] = 0;
            $res['msg'] = 'Errors';
        } else {
            $res['status'] = 1;
            $res['msg'] = 'Success';
            $res['obj'] = selectOne($table, ['ID' => $productID]);
        }

        dd($res);
        exit();
    }

    public static function deleteProduct()
    {
        checkRequest('DELETE');
        $table = 'product';
        adminOnly();
        parse_str(file_get_contents("php://input"), $sent_vars);
        if (isset($sent_vars['ID'])) {
            $id['ID'] = $sent_vars['ID'];
            $status = delete($table, $id);
            if ($status == 1) {
                $res['status'] = 1;
                $res['msg'] = 'Success';
                dd($res);
                exit();
            }


            $res['status'] = 0;
            $res['msg'] = 'Not found product by ID';

            dd($res);
            exit();
        }
    }
    public static function updateProduct()
    {
        checkRequest('PUT');
        $table = 'product';
        adminOnly();

        parse_str(file_get_contents("php://input"), $sent_vars);
        $id['ID'] =  $sent_vars['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['ID']);
        $res['status'] = 1;
        $res['msg'] = update($table, $id, $sent_vars);
        dd($res);
        exit();
    }
}