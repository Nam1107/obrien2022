<?php
require './controllers/cart.php';

class order
{

    public static function createOrder()
    {
        # code...
        checkRequest('POST');
        userOnly();
        $cart = cart::userCart()['obj'];
        if (!$cart) {
            $res['status'] = 0;
            $res['errors'] = 'Your cart is empty ';
            dd($res);
            exit();
        }
        foreach ($cart as $key => $val) {
            if ($val['status'] === 0) {
                $res['status'] = 0;
                $res['errors'] = 'Some items in your cart has sold out ';
                dd($res);
                exit();
            }
        }
        foreach ($cart as $key => $val) {
            $quanity = $val['quanity'];
            $productID = $val['productID'];
            custom("
            UPDATE product SET stock = if(stock < $quanity,0, stock - $quanity), sold = if(sold IS NULL, $quanity , sold + $quanity) WHERE ID = $productID
            ");
        }
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $userID = $_SESSION['user']['ID'];

        delete('shoppingCart', ['userID' => $userID]);
        $sent_vars['userID'] = $userID;
        $orderID = create('order', $sent_vars);

        foreach ($cart as $key => $val) {
            $condition = [
                "orderID" => $orderID,
                "productID" => $val['productID'],
                "unitPrice" => $val['unitPrice'],
                "quanity" => $val["quanity"]
            ];
            create('orderDetail', $condition);
        }
        $res['order'] = selectOne('order', ["ID" => $orderID]);
        $res['obj']  = selectAll('orderDetail', ['orderID' => $orderID]);
        dd($res);
        exit();
    }
}