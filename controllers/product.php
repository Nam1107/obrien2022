<?php
require './database/db.php';
require './helper/middleware.php';

class Product
{

    public static function ListProduct()
    {
        // checkRequest('GET');
        $table = 'product';
        $res['status'] = 1;
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $category = $sent_vars['category'];
        $sale = $sent_vars['sale'];
        $sortBy = $sent_vars['sortBy'];
        switch ($sent_vars['name']) {
            case 'price':
                $name = 'curPrice';
                break;
            default:
                $name = $sent_vars['name'];
                break;
        }

        $sortType = 'ASC';
        if (isset($sent_vars['sortType'])) {
            $sortType = $sent_vars['sortType'];
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
        $obj = custom(
            "SELECT A.* , category.name AS category, IF(A.statusSale = '1', A.priceSale, A.price) AS curPrice
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


        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = $page;
        $res['obj'] = $obj;

        dd($res);
        exit();
    }
    public static function getProduct($id)
    {
        checkRequest('GET');
        $table = 'product';
        $res['status'] = 1;

        $userID = 0;
        if (authenToken()) {
            $userID = $_SESSION['user']['ID'];
        }

        $obj = custom("
            SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND A.ID = $id
        
        ");

        $wish = custom("
        SELECT *
            FROM wishList
            WHERE userID = $userID
            AND productID = $id
        ");

        if (!$wish) {
            $obj[0]['wishList'] = 0;
        } else $obj[0]['wishList'] = 1;


        $gallery = selectAll('gallery', ['productID' => $id]);
        $obj[0]['gallery'] = $gallery;
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

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $sent_vars['createdAt'] = currentTime();
        $sent_vars['updatedAt'] = currentTime();
        $cate = $sent_vars['category'];
        $urls = $sent_vars['gallery'];
        $category = custom("
        SELECT * FROM category WHERE name LIKE '%$cate%' 
        ");
        unset($sent_vars['category'], $sent_vars['gallery']);
        $sent_vars['categoryID'] = $category[0]['ID'];
        $id = create($table, $sent_vars);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $obj = custom("
            SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND A.ID = $id

        ");

        foreach ($urls as $key => $url) :
            $image['productID'] = $id;
            $image['URLImage'] =  $url;
            create('gallery', $image);
        endforeach;
        // $gallery = selectAll('gallery', ['productID' => $id]);
        $gallery = custom("
        select ID,URLImage from gallery where productID = $id
        ");
        $obj[0]['gallery'] = $gallery;
        $res['obj'] = $obj[0];

        dd($res);
        exit();
    }

    public static function deleteProduct($id)
    {
        checkRequest('DELETE');
        $table = 'product';
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $productID['ID'] = $id;
        delete($table, $productID);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
    public static function updateProduct($id)
    {
        checkRequest('PUT');
        $table = 'product';
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $productID['ID'] =  $id;
        $sent_vars['updatedAt'] = currentTime();
        $res['status'] = 1;
        update($table, $productID, $sent_vars);
        $res['msg'] = 'Success';
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
}