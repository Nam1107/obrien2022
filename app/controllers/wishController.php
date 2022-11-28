<?php

class wishController extends Controllers
{
    public $middle_ware;
    public $wishlist_model;
    public $render_view;
    public function __construct()
    {
        $this->wishlist_model = $this->model('wishModel');
        $this->middle_ware = new middleware();
        $this->render_view = $this->render('renderView');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }

    public function getWishList()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $sent_vars = $_GET;
        try {
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res = $this->wishlist_model->getList($userID, $page, $perPage);

        $this->render_view->ToView($res);
        exit();
    }

    public function removeProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $check = $this->wishlist_model->getDetail($userID, $id);
        if (!$check) {
            $this->render_view->loadErrors(404, 'Not found this product in your wishlist');
        }
        $condition = [
            "userID" => $userID,
            "productID" => $id
        ];
        delete('wishList', $condition);
        $wishList = custom("
        SELECT A.* , category.name AS category
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
        FROM product) AS A,category,wishList
        WHERE A.categoryID = category.ID
        AND A.ID = wishList.productID
        AND wishList.userID = $userID
        ");
        $res['obj'] = $wishList;
        $this->render_view->ToView($res);
        exit();
    }

    public function addProduct($id = 0)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
        $check = selectOne('product', ['ID' => $id]);
        if (!$check) {
            $this->render_view->loadErrors(400, 'This product does not exist');
        }
        $userID = $_SESSION['user']['ID'];

        $condition = [
            "userID" => $userID,
            "productID" => $id
        ];
        $check = selectOne('wishList', $condition);
        if (!$check) {
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
            $res['obj'] = $wishList;
            $this->render_view->ToView($res);
            exit();
        } else {
            $this->render_view->loadErrors(400, 'This product has exists in your list');
        }
    }
}