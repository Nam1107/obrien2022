<?php

class orderController extends Controllers
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
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function createNewOrder($userID, $note, $phone, $address)
    {
        $order['userID'] = $userID;
        $order['note'] = $note;
        $order['status'] = 'To Ship';
        $order['phone'] = $phone;
        $order['address'] = $address;
        $order['createdAt'] = currentTime();

        $orderID = create('order', $order);
        return $orderID;
    }
    public function createOrderDetail($orderID, $productID, $unitPrice, $quantity)
    {
        $condition = [
            "orderID" => $orderID,
            "productID" => $productID,
            "unitPrice" => $unitPrice,
            "quantity" => $quantity,
            "createdAt" => currentTime()
        ];
        create('orderDetail', $condition);
    }
    public function updateStatus($orderID, $status, $description)
    {
        update('order', ['ID' => $orderID], ['status' => $status]);
        $shipping = [
            "orderID" => $orderID,
            "description" => $description,
            "createdAt" => currentTime()
        ];
        create('shippingDetail', $shipping);
    }
    function listStatus()
    {
        $this->middle_ware->checkRequest('GET');
        dd(status_order);
        exit();
    }
    function report()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $sent_vars = $_GET;


        try {
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $report = custom("SELECT A.status,SUM(A.total) AS total,COUNT(A.ID) AS numOfOrder
        FROM 
        (SELECT `order`.ID,`order`.status,`order`.createdAt,SUM(unitPrice*quantity) AS total
        FROM orderDetail,`order`
        WHERE orderID = `order`.ID
        AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
        GROUP BY orderID) AS A
        GROUP BY A.status
        ");


        $status = array_column($report, 'status');

        $obj = array();

        // $key = 1;

        foreach (status_order as $key => $val) {
            $check = $this->find(status_order[$key], $status);
            if ($check !== null) {
                $value['status'] = status_order[$key];
                $value['total'] = $report[$check]['total'];
                $value['numOfOrder'] = $report[$check]['numOfOrder'];
                // $value['numOfProduct'] = $report[$check]['numOfProduct'];
            } else {
                $value['status'] = status_order[$key];
                $value['total'] = 0;
                $value['numOfOrder'] = 0;
                // $value['numOfProduct'] = 0;
            }
            array_push($obj, $value);
        }

        $res['report'] = $obj;
        dd($res);
        exit;
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
            $this->loadErrors(400, 'Error: input is invalid');
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
            $quantity = $val['quantity'];
            $productID = $val['productID'];
            custom("
            UPDATE product SET stock = if(stock < $quantity,0, stock - $quantity), sold = if(sold IS NULL, $quantity , sold + $quantity) WHERE ID = $productID
            ");
        }
        #delete cart
        delete('shoppingCart', ['userID' => $userID]);

        #create order
        $orderID = $this->createNewOrder($userID, $sent_vars['note'], $sent_vars['phone'], $sent_vars['address']);

        $this->shipping_model->create($orderID);

        foreach ($cart as $key => $val) {
            $this->createOrderDetail($orderID, $val['productID'], $val['unitPrice'], $val["quantity"]);
        }
        $res = $this->order_model->getDetail($orderID);
        dd($res);
        exit();
    }

    public function myListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];

        $sent_vars = $_GET;

        try {
            $status = $sent_vars['status'];
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->ListOrderByUser($userID, $status, $page, $perPage);
        dd($res);
        exit();
    }

    public function adminListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        // $json = file_get_contents("php://input");
        // $sent_vars = json_decode($json, TRUE);
        $sent_vars = $_GET;
        try {
            $status = $sent_vars['status'];
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->listOrder($status, $page, $perPage, $startDate, $endDate);
        dd($res);
        exit();
    }

    public function getMyOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $res = $this->order_model->getDetail($id);
        dd($res);
        exit();
    }

    public function adminGetOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res = $this->order_model->getDetail($id, 1);
        dd($res);
        exit();
    }

    public function setStatusOrder($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
        }

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        if (empty($sent_vars['status']) || empty($sent_vars['description'])) {
            $this->loadErrors(400, 'Not enough value');
        }

        $this->updateStatus($id, $sent_vars['status'], $sent_vars['description']);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }

    public function cancelOrder($id = 0)
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
            $this->loadErrors(400, 'Error: input is invalid');
        }
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
        }
        switch ($order['status']) {
            case 'To Ship':
                $this->updateStatus($id, $status, $reason);
                $res['msg'] = 'Success';
                dd($res);
                exit();
                break;
            case 'To Recivie':
                $this->loadErrors(400, 'The order is being shipped');
                exit;
                break;
            default:
                $this->loadErrors(400, 'The order has been delivered');
                exit;
                break;
        }
    }
    public function orderRecevied($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();

        $status = 'To Rate';
        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
            exit();
        }

        switch ($order['status']) {
            case 'To Recivie':
                $this->updateStatus($id, $status, "Confirm Receipt of an Order from a Customer");
                $res['msg'] = 'Success';
                dd($res);
                exit();
            case 'To Ship':
                $this->loadErrors(400, 'Orders are being prepared');
                exit();
            default:
                $this->loadErrors(400, 'The order has been completed');
                exit();
        }
    }
}