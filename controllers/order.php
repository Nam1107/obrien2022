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

        $order['userID'] = $userID;
        $order['note'] = $sent_vars['note'];
        $order['status'] = 'To Ship';
        $order['email'] = $sent_vars['email'];
        $order['phone'] = $sent_vars['phone'];
        $order['address'] = $sent_vars['address'];
        $order['address'] = currentTime();


        $orderID = create('order', $order);

        foreach ($cart as $key => $val) {
            $condition = [
                "orderID" => $orderID,
                "productID" => $val['productID'],
                "unitPrice" => $val['unitPrice'],
                "quanity" => $val["quanity"]
            ];
            create('orderDetail', $condition);
        }
        $res['status'] = 1;
        $res['order'] = selectOne('order', ["ID" => $orderID]);
        $res['obj']  = selectAll('orderDetail', ['orderID' => $orderID]);
        dd($res);
        exit();
    }

    public static function myListOrder()
    {
        checkRequest('GET');
        userOnly();
        $userID = $_SESSION['user']['ID'];

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $sent_vars['userID'] = $userID;

        $status = '';
        if (isset($sent_vars['status'])) {
            $status = $sent_vars['status'];
        }

        $order = custom("
        SELECT `order`.ID,`order`.status , `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.userID = $userID
        AND `order`.status like '%$status%'
        GROUP BY
        `orderDetail`.orderID
        ");

        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = "No orders yet";
            dd($res);
            exit();
        }

        foreach ($order as $key => $obj) {
            $val = $obj['ID'];
            $order[$key]['product'] = custom("SELECT product.ID, product.image,product.name,unitPrice,quanity
            FROM `product`,`orderDetail`	
            WHERE `product`.ID = orderDetail.productID
            AND orderID = $val
            ");
        }
        $res['status'] = 1;
        $res['obj'] = $order;
        dd($res);
        exit();
    }

    public static function ListOrder()
    {
        checkRequest('GET');
        adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $status = '';
        if (isset($sent_vars['status'])) {
            $status = $sent_vars['status'];
        }

        if (!isset($sent_vars['page'])  || $sent_vars['page'] <= 0) {
            $page = 1;
        } else {
            $page = $sent_vars['page'];
        }
        $perPage = 10;
        if (isset($sent_vars['perPage'])) {
            $perPage = $sent_vars['perPage'];
        }

        $offset = $perPage * ($page - 1);

        $order = custom("
        SELECT `order`.ID,`order`.userID,user.name,`order`.status , `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`,user
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.status LIKE '%$status%'
        AND user.ID = `order`.userID
        GROUP BY `orderDetail`.orderID
        ORDER BY `order`.createdAt DESC
        LIMIT $perPage  OFFSET $offset 
        ");
        $res['status'] = 1;
        $res['obj'] = $order;
        dd($res);
        exit();
    }

    public static function myOrderByID($id)
    {
        checkRequest('GET');
        userOnly();
        $userID = $_SESSION['user']['ID'];

        $order = custom("
        SELECT `order`.ID,`order`.status , `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.userID = $userID
        AND `order`.ID = $id
        GROUP BY
        `orderDetail`.orderID
        ");

        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = "No orders yet";
            dd($res);
            exit();
        }

        $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quanity
        FROM `product`,`orderDetail`	
        WHERE `product`.ID = orderDetail.productID
        AND orderID = $id
        ");

        $res['status'] = 1;
        $res['obj'] = $order[0];
        $res['obj']['product'] = $product;
        dd($res);
        exit();
    }

    public static function OrderByID($id)
    {
        checkRequest('GET');
        adminOnly();
        $order = custom("
        SELECT `order`.ID,`order`.status ,`order`.userID, `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.ID = $id
        GROUP BY
        `orderDetail`.orderID
        ");

        $user = selectOne('user', ['ID' => $order[0]['userID']]);

        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = "No orders yet";
            dd($res);
            exit();
        }

        $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quanity
        FROM `product`,`orderDetail`	
        WHERE `product`.ID = orderDetail.productID
        AND orderID = $id
        ");

        $res['status'] = 1;
        $res['obj'] = $order[0];
        $res['obj']['product'] = $product;
        $res['obj']['user'] = $user;
        dd($res);
        exit();
    }

    public static function setStatusOrder($id)
    {
        checkRequest('PUT');
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        if (!isset($sent_vars['status'])) {
            $res['status'] = 0;
            $res['errors'] = "Not enough value";
            dd($res);
            exit();
        }
        $status = $sent_vars['status'];
        update('order', ['ID' => $id], ['status' => $status]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public static function cancelOrder($id)
    {
        checkRequest('PUT');
        userOnly();

        $status = 'Cancelled';
        update('order', ['ID' => $id], ['status' => $status]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
}