<?php
require './database/db.php';
require './helper/middleware.php';
class order
{
    public static function getCart()
    {
        $table = 'shoppingCart';
        $res['status'] = 1;
        $id = 0;
        if (isset($_SESSION['user'])) {
            $id = $_SESSION['user']['ID'];
        }

        $shoppingCart = custom("
        SELECT shoppingCart.* ,  A.unitPrice
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(),product.priceSale, product.price) AS unitPrice
        FROM product) AS A,shoppingCart
        WHERE A.ID = shoppingCart.productID
        AND userID = $id
        ");
        $res['obj'] = $shoppingCart;
        dd($res);
        exit();
    }

    public static function addToCart()
    {
        checkRequest('POST');
        userOnly();
        $table = 'shoppingCart';
        $id = $_SESSION['user']['ID'];
        $condition = [
            'userID' => $id,
            'productID' => $_POST['productID'],
        ];
        $obj = selectOne($table, $condition);
        if (!$obj) {
            $condition['quanity'] = $_POST['quanity'];
            create($table, $condition);
        } else {
            $quanity['quanity'] = $obj['quanity'] + $_POST['quanity'];
            update($table, ['ID' => $obj['ID']], $quanity);
        }
        $shoppingCart = custom("
        SELECT shoppingCart.* ,  A.unitPrice
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(),product.priceSale, product.price) AS unitPrice
        FROM product) AS A,shoppingCart
        WHERE A.ID = shoppingCart.productID
        AND userID = $id
        ");
        $res['obj'] = $shoppingCart;
        // $res['obj'] = $obj;
        dd($res);
        exit();
    }

    public static function newOder()
    {
        checkRequest('POST');
        userOnly();
    }
}