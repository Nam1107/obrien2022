<?php
require './app/controllers/cart.php';

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

        if (!isset($sent_vars['note']) || !isset($sent_vars['phone']) || !isset($sent_vars['address'])) {
            $res['status'] = 0;
            $res['errors'] = "Not enough parameters ";
            dd($res);
            exit();
        }

        $order['userID'] = $userID;
        $order['note'] = $sent_vars['note'];
        $order['status'] = 'To Ship';
        $order['phone'] = $sent_vars['phone'];
        $order['address'] = $sent_vars['address'];
        $order['createdAt'] = currentTime();

        $orderID = create('order', $order);
        $shipping = [
            "orderID" => $orderID,
            "description" => "Order has been created",
            "createdAt" => currentTime()
        ];
        create('shippingDetail', $shipping);

        foreach ($cart as $key => $val) {
            $condition = [
                "orderID" => $orderID,
                "productID" => $val['productID'],
                "unitPrice" => $val['unitPrice'],
                "quanity" => $val["quanity"],
                "createdAt" => currentTime()
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
        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $offset = $perPage * ($page - 1);

        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT `order`.ID
                FROM `order`
                WHERE `order`.status LIKE '%$status%'
                AND `order`.userID = $userID
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.ID,`order`.status ,C.description,C.createdAt AS lastUpdated, `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	,(
        SELECT shippingDetail.*
        FROM (SELECT max(ID) AS curID
        from shippingDetail
        group by orderID) AS B, shippingDetail
        WHERE curID = ID
        ) AS C
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.userID = $userID
        AND `order`.status like '%$status%'
        AND C.orderID = `order`.ID
        GROUP BY
        `orderDetail`.orderID
        LIMIT $perPage  OFFSET $offset 
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
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['obj'] = $order;
        dd($res);
        exit();
    }

    public static function adminListOrder()
    {
        checkRequest('GET');
        adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $status = $sent_vars['status'];
        $startDate = $sent_vars['startDate'];
        $endDate = $sent_vars['endDate'];

        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $offset = $perPage * ($page - 1);


        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT `order`.ID
                FROM `order`
                WHERE `order`.status LIKE '%$status%'
                AND `order`.createdAt > $startDate AND  `order`.createdAt < $endDate
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.ID,`order`.userID,user.name,`order`.status , `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quanity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`,user
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.status LIKE '%$status%'
        AND user.ID = `order`.userID
        AND `order`.createdAt > $startDate AND  `order`.createdAt < $endDate
        GROUP BY `orderDetail`.orderID
        ORDER BY `order`.createdAt DESC
        LIMIT $perPage  OFFSET $offset 
        ");
        $res['status'] = 1;
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;

        $res['obj'] = $order;
        dd($res);
        exit();
    }

    public static function getMyOrder($id)
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

        $shipping = custom("SELECT shippingDetail.description,shippingDetail.createdAt
        from shippingDetail
        WHERE orderID =  $id
        ");

        $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quanity
        FROM `product`,`orderDetail`	
        WHERE `product`.ID = orderDetail.productID
        AND orderID = $id
        ");

        $res['status'] = 1;
        $res['obj'] = $order[0];
        $res['obj']['shipping'] = $shipping;
        $res['obj']['product'] = $product;
        dd($res);
        exit();
    }

    public static function adminGetOrder($id)
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

        $shipping = custom("SELECT shippingDetail.description,shippingDetail.createdAt
        from shippingDetail
        WHERE orderID =  $id
        ");

        $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quanity
        FROM `product`,`orderDetail`	
        WHERE `product`.ID = orderDetail.productID
        AND orderID = $id
        ");

        $res['status'] = 1;
        $res['obj'] = $order[0];
        $res['obj']['user'] = $user;
        $res['obj']['shipping'] = $shipping;
        $res['obj']['product'] = $product;

        dd($res);
        exit();
    }

    public static function setStatusOrder($id)
    {
        checkRequest('PUT');
        adminOnly();

        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = ' No orders yet';
            dd($res);
            exit();
        }

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $desc = $sent_vars['description'];

        if (!isset($sent_vars['status'])) {
            $res['status'] = 0;
            $res['errors'] = "Not enough value";
            dd($res);
            exit();
        }
        $status = $sent_vars['status'];
        update('order', ['ID' => $id], ['status' => $status]);
        $shipping = [
            "orderID" => $id,
            "description" => $desc,
            "createdAt" => currentTime()
        ];
        create('shippingDetail', $shipping);
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
        $order = selectOne('order', ['ID' => $id]);
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $reason = $sent_vars['reason'];
        $reason = "Reason for Cancellation : " . $reason;
        if (!isset($sent_vars['reason'])) {
            $res['status'] = 0;
            $res['errors'] = "Not enough parameters ";
            dd($res);
            exit();
        }
        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = ' No orders yet';
            dd($res);
            exit();
        }
        switch ($order['status']) {
            case 'To Ship':
                update('order', ['ID' => $id], ['status' => $status]);
                $shipping = [
                    "orderID" => $id,
                    "description" => $reason,
                    "createdAt" => currentTime()
                ];
                create('shippingDetail', $shipping);
                $res['status'] = 1;
                $res['msg'] = 'Success';
                dd($res);
                exit();
                break;
            case 'To Recivie':
                $res['status'] = 0;
                $res['errors'] = ' The order is being shipped';
                dd($res);
                exit();
                break;
            default:
                $res['status'] = 0;
                $res['errors'] = ' The order has been delivered';
                dd($res);
                exit();
        }
    }
    public static function orderRecevied($id)
    {
        checkRequest('PUT');
        userOnly();

        $status = 'To Rate';
        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = ' No orders yet';
            dd($res);
            exit();
        }
        if ($order['status'] == 'To Ship' || $order['status'] == 'To Recivie') {
            update('order', ['ID' => $id], ['status' => $status]);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['errors'] = 'The order has been completed';
            dd($res);
            exit();
        }
    }
}