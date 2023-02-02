<?php

class wishController extends Controllers
{
    public $middle_ware;
    public $wishlist_model;
    public $product_model;
    public function __construct()
    {
        $this->wishlist_model = $this->model('wishModel');
        $this->middle_ware = new middleware();
        $this->product_model = $this->model('productModel');
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
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        $res = $this->wishlist_model->getList($userID, $page, $perPage);

        $this->ToView($res);
        exit();
    }

    public function removeProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $check = $this->wishlist_model->getDetail($userID, $id);
        if (!$check) {
            $this->loadErrors(404, 'Not found this product in your wishlist');
        }
        $condition = [
            "userID" => $userID,
            "productID" => $id
        ];
        delete('wishList', $condition);

        $res['msg'] = 'Success';
        $this->ToView($res);
        exit();
    }

    public function addProduct($id = 0)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
        $check = $this->product_model->getDetail($id, 1);
        if (!$check) {
            $this->loadErrors(400, 'This product does not exist');
        }
        $userID = $_SESSION['user']['ID'];
        $check = $this->wishlist_model->getDetail($userID, $id);
        if (!$check) {
            $condition['createdAt'] = currentTime();
            create('wishList', $condition);

            $res['msg'] = 'Success';
            $this->ToView($res);
            exit();
        } else {
            $this->loadErrors(400, 'This product has exists in your list');
        }
    }
}