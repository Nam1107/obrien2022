<?php
require './database/db.php';
require './helper/middleware.php';

class wishList
{

    public static function getWishList()
    {
        checkRequest('GET');
        userOnly();
        $userID = $_SESSION['user']['ID'];
        $wishList = custom("
        SELECT A.* , category.name AS category
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
        FROM product) AS A,category,wishList
        WHERE A.categoryID = category.ID
        AND A.ID = wishList.productID
        AND wishList.userID = $userID
        ");
        $res['status'] = 1;
        $res['obj'] = $wishList;
        dd($res);
        exit();
    }

    public static function removeProduct($id)
    {
        checkRequest('DELETE');
        userOnly();
        $userID = $_SESSION['user']['ID'];
        $condition = [
            "userID" => $userID,
            "productID" => $id
        ];
        $check = selectOne('wishList', $condition);
        if (!$check) {
            $res['status'] = 0;
            $res['errors'] = 'Not found this product in your wishlist';
            dd($res);
            exit();
        }
        delete('wishList', $condition);
        $wishList = custom("
        SELECT A.* , category.name AS category
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
        FROM product) AS A,category,wishList
        WHERE A.categoryID = category.ID
        AND A.ID = wishList.productID
        AND wishList.userID = $userID
        ");
        $res['status'] = 1;
        $res['obj'] = $wishList;
        dd($res);
        exit();
    }

    public static function addProduct($id)
    {
        checkRequest('POST');
        userOnly();
        $check = selectOne('product', ['ID' => $id]);
        if (!$check) {
            $res['status'] = 0;
            $res['errors'] = 'This product does not exist';
            dd($res);
            exit();
        }
        $userID = $_SESSION['user']['ID'];

        $condition = [
            "userID" => $userID,
            "productID" => $id
        ];
        $check = selectOne('wishList', $condition);
        if (!$check && $id != 0) {
            $condition['createdAt'] = currentTime();
            create('wishList', $condition);
            $wishList = custom("
            SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category,wishList
            WHERE A.categoryID = category.ID
            AND A.ID = wishList.productID
            AND wishList.userID = $userID
            ");
            $res['status'] = 1;
            $res['obj'] = $wishList;
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['errors'] = 'This product has exists in your list';
            dd($res);
            exit();
        }
    }
}