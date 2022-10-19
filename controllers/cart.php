<?php
require './database/db.php';
require './helper/middleware.php';
class cart
{
    public static function userCart()
    {
        $id = 0;
        if (authenToken()) {
            $id = $_SESSION['user']['ID'];
        }

        $shoppingCart = custom("
        SELECT shoppingCart.productID,product.name,product.image ,shoppingCart.quanity,  A.unitPrice, unitPrice*quanity AS subTotal,IF(quanity<A.stock,1, 0) AS status
        FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(),product.priceSale, product.price) AS unitPrice
        FROM product) AS A,shoppingCart,product
        WHERE A.ID = shoppingCart.productID
        AND userID = $id
        AND shoppingCart.productID = product.ID
        AND product.IsPublic = 1
        ");
        $total = 0;
        foreach ($shoppingCart as $key => $val) {
            $total = $total + $val['subTotal'];
        }
        $res['obj'] = $shoppingCart;
        $res['total'] = $total;
        return $res;
    }

    public static function getCart()
    {
        $res['status'] = 1;
        $cart = cart::userCart();
        $res['obj'] = $cart['obj'];
        $res['total'] = $cart['total'];
        dd($res);
        exit();
    }

    public static function addProduct($id)
    {
        checkRequest('POST');
        userOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $table = 'shoppingCart';
        $userID = $_SESSION['user']['ID'];
        $pro = selectOne('product', ['ID' => $id, 'IsPublic' => '1']);
        if (!$pro) {
            $res['status'] = 0;
            $res['errors'] = 'Not found product';
            dd($res);
            exit();
        }
        $condition = [
            'userID' => $userID,
            'productID' => $id,
        ];
        $obj = selectOne($table, $condition);
        if (!$obj) {
            $condition['quanity'] = $sent_vars['quanity'];
            if ($condition['quanity'] > 6) {
                $condition['quanity'] = 6;
            }
            create($table, $condition);
            cart::getCart();
        }

        if ($obj['quanity'] > 5) {
            $res['status'] = 0;
            $res['errors'] = 'You cannot add more than 6 quantities of this product';
            dd($res);
            exit();
        }
        $quanity['quanity'] = $obj['quanity'] + $sent_vars['quanity'];
        if ($quanity['quanity'] > 6) {
            $quanity['quanity'] = 6;
        }
        update($table, ['ID' => $obj['ID']], $quanity);
        cart::getCart();
    }

    public static function removeProduct($id)
    {
        checkRequest('DELETE');
        userOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $table = 'shoppingCart';
        $condition = [
            'userID' => $_SESSION['user']['ID'],
            'productID' => $id,

        ];
        delete($table, $condition);
        cart::getCart();
    }

    public static function incrementByOne($id)
    {
        checkRequest('PUT');
        userOnly();
        $table = 'shoppingCart';
        $userID = $_SESSION['user']['ID'];
        $condition = [
            'userID' => $userID,
            'productID' => $id,
        ];
        $obj = selectOne($table, $condition);
        if (!$obj) {
            $res['status'] = 0;
            $res['errors'] = 'Cannot found product in your cart';
            dd($res);
            exit();
        }
        if ($obj['quanity'] > 5) {
            $res['status'] = 0;
            $res['errors'] = 'You cannot add more than 6 quantities of this product';
            dd($res);
            exit();
        }
        custom("
        UPDATE shoppingCart SET quanity = if(quanity < 6,quanity + 1, 6) WHERE userID = $userID AND productID = $id
        ");
        cart::getCart();
    }

    public static function decrementByOne($id)
    {
        checkRequest('PUT');
        userOnly();
        $table = 'shoppingCart';
        $userID = $_SESSION['user']['ID'];
        $condition = [
            'userID' => $userID,
            'productID' => $id,
        ];
        $obj = selectOne($table, $condition);
        if (!$obj) {
            $res['status'] = 0;
            $res['errors'] = 'Cannot found product in your cart';
            dd($res);
            exit();
        }
        custom("
        UPDATE shoppingCart SET quanity = if(quanity > 1 ,quanity - 1, 1) WHERE userID = $userID AND productID = $id
        ");
        cart::getCart();
    }
}