<?php

class order extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $order_model;
    public function __construct()
    {
        $this->order_model = $this->model('orderModel');
        $this->cart_model = $this->model('cartModel');
        $this->shipping_model = $this->model('shippingModel');
        $this->middle_ware = new middleware();
    }

    public function createOrder()
    {
        # code...
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();

        $userID = $_SESSION['user']['ID'];
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        #check...
        if (!isset($sent_vars['note']) || empty($sent_vars['phone']) || empty($sent_vars['address'])) {
            $this->loadErrors(400, 'Not enough parameters');
        }
        $cart = $this->cart_model->getCart($userID)['obj'];
        if (!$cart) {
            $this->loadErrors(400, 'Your cart is empty');
        }
        foreach ($cart as $key => $val) {
            if ($val['status'] === 0) {
                $this->loadErrors(400, 'Some items in your cart has sold out');
            }
        }

        #update sold of product
        foreach ($cart as $key => $val) {
            $quanity = $val['quanity'];
            $productID = $val['productID'];
            custom("
            UPDATE product SET stock = if(stock < $quanity,0, stock - $quanity), sold = if(sold IS NULL, $quanity , sold + $quanity) WHERE ID = $productID
            ");
        }
        #delete cart
        $this->cart_model->delete($userID);

        #create order
        $orderID = $this->order_model->createOrder($userID, $sent_vars['note'], $sent_vars['phone'], $sent_vars['address']);

        $this->shipping_model->create($orderID);

        foreach ($cart as $key => $val) {
            $this->order_model->createOrderDetail($orderID, $val['productID'], $val['unitPrice'], $val["quanity"]);
        }
        $res['status'] = 1;
        $res['obj'] = $this->order_model->getDetail($orderID);
        dd($res);
        exit();
    }

    public function myListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
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
            $this->loadErrors(400, 'No orders yet');
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

    public function adminListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
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

    public function getMyOrder($id)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
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
            $this->loadErrors(400, 'No orders yet');
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

    public function adminGetOrder($id)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
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
            $this->loadErrors(400, 'No orders yet');
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

    public function setStatusOrder($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
        }

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $desc = $sent_vars['description'];

        if (!isset($sent_vars['status'])) {
            $this->loadErrors(400, 'Not enough value');
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

    public function cancelOrder($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();

        $status = 'Cancelled';
        $order = selectOne('order', ['ID' => $id]);
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $reason = $sent_vars['reason'];
        $reason = "Reason for Cancellation : " . $reason;
        if (!isset($sent_vars['reason'])) {
            $this->loadErrors(400, 'Not enough parameters');
        }
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
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
                $this->loadErrors(400, 'The order is being shipped');
                break;
            default:
                $this->loadErrors(400, 'The order has been delivered');
                break;
        }
    }
    public function orderRecevied($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();

        $status = 'To Rate';
        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
            exit();
        }
        if ($order['status'] == 'To Ship' || $order['status'] == 'To Recivie') {
            update('order', ['ID' => $id], ['status' => $status]);
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        } else {
            $this->loadErrors(400, 'The order has been completed');
            exit();
        }
    }
}