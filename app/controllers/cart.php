<?php

class cart extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $cart_model;
    public function __construct()
    {
        $this->cart_model = $this->model('cartModel');
        $this->product_model = $this->model('productModel');
        $this->middle_ware = new middleware();
    }
    public function getCart()
    {
        $userID = 0;
        $obj = $this->middle_ware->authenToken();
        if ($obj['status'] == 1) {
            $userID = $_SESSION['user']['ID'];
        }
        $res = $this->cart_model->getCart($userID);
        dd($res);
        exit();
    }

    public function addProduct($id = 0)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        if (empty($sent_vars['quanity'])) {
            $this->loadErrors(400, 'Not enough value');
        }
        $table = 'shoppingCart';
        $userID = $_SESSION['user']['ID'];
        $this->product_model->checkProduct($id, 1);

        $condition = [
            'userID' => $userID,
            'productID' => $id,
        ];
        $obj = selectOne('shoppingCart', $condition);



        if (!$obj) {
            $res = $this->cart_model->getCart($userID);
            if (count($res) > 10) {
                $this->loadErrors(400, 'Your cart is full of slot');
            }

            $condition['quanity'] = $sent_vars['quanity'];
            if ($condition['quanity'] > 6) {
                $condition['quanity'] = 6;
            }
            create($table, $condition);
            $res = $this->cart_model->getCart($userID);
            dd($res);
            exit();
        }

        if ($obj['quanity'] > 5) {
            $this->loadErrors(400, 'You cannot add more than 6 quantities of this product');
        }

        $quanity['quanity'] = $obj['quanity'] + $sent_vars['quanity'];
        if ($quanity['quanity'] > 6) {
            $quanity['quanity'] = 6;
        }
        update($table, ['ID' => $obj['ID']], $quanity);
        $res = $this->cart_model->getCart($userID);
        dd($res);
        exit();
    }

    public function removeProduct($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $this->product_model->checkProduct($id, 1);
        $obj = $this->cart_model->getProductInCart($userID, $id);

        $table = 'shoppingCart';
        $condition = [
            'userID' => $userID,
            'productID' => $id,
        ];
        delete($table, $condition);
        $res = $this->cart_model->getCart($userID);
        dd($res);
        exit();
    }

    public function incrementByOne($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $obj = $this->cart_model->getProductInCart($userID, $id);
        custom("
        UPDATE shoppingCart SET quanity = if(quanity < 6,quanity + 1, 6) WHERE userID = $userID AND productID = $id
        ");
        $res = $this->cart_model->getCart($id);
        dd($res);
        exit();
    }

    public function decrementByOne($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();
        $table = 'shoppingCart';
        $userID = $_SESSION['user']['ID'];
        $obj = $this->cart_model->getProductInCart($userID, $id);

        custom("
        UPDATE shoppingCart SET quanity = if(quanity > 1 ,quanity - 1, 1) WHERE userID = $userID AND productID = $id
        ");
        $res = $this->cart_model->getCart($id);
        dd($res);
        exit();
    }
}