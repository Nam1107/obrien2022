<?php
require './database/db.php';
require './helper/middleware.php';

class wishList
{
    public static function getWishList()
    {
        $id = 0;
        if (authenToken()) {
            $id = $_SESSION['user']['ID'];
        }

        $wishList = custom("
        SELECT A.* , category.name AS category
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
        FROM product) AS A,category,wishList
        WHERE A.categoryID = category.ID
        AND A.ID = wishList.productID
        AND wishList.userID = $id
        ");
        $res['obj'] = $wishList;
        return $res;
    }
}