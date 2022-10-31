<?php

class wishList extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $wishlist_model;
    public function __construct()
    {
        $this->wishlist_model = $this->model('wishListModel');
        $this->middle_ware = new middleware();
    }

    public function getWishList()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $offset = $perPage * ($page - 1);

        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT wishList.ID
                FROM `wishList`
                WHERE wishList.userID = $userID
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $wishList = custom("
        SELECT A.* , category.name AS category
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
        FROM product) AS A,category,wishList
        WHERE A.categoryID = category.ID
        AND A.ID = wishList.productID
        AND wishList.userID = $userID
        LIMIT $perPage OFFSET $offset
        ");

        $res['status'] = 1;
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['obj'] = $wishList;
        dd($res);
        exit();
    }

    public function removeProduct($id)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->userOnly();
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

    public function addProduct($id)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
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