<?php

class orderController extends Controllers
{
    public $middle_ware;
    public $order_model;
    public $delivery_model;
    public $shipping_model;
    public $cart_model;
    public function __construct()
    {
        $this->order_model = $this->model('orderModel');
        $this->cart_model = $this->model('cartModel');
        $this->delivery_model = $this->model('deliveryModel');
        $this->shipping_model = $this->model('shippingModel');
        $this->middle_ware = new middleware();
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }



    function listStatus()
    {
        $this->middle_ware->checkRequest('GET');
        $statusArr = array_values(array_filter(status_order));
        $this->ToView($statusArr);
        exit();
    }

    function cancelReason()
    {
        $this->middle_ware->checkRequest('GET');
        $this->ToView(cancel_reason);
        exit;
    }

    function orderFail()
    {
        $this->middle_ware->checkRequest('GET');
        $this->ToView(order_fail);
        exit;
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

        $report = custom("SELECT A.status,CAST(SUM(A.total) AS FLOAT) AS total,COUNT(A.ID) AS numOfOrder
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
        $this->ToView($res);
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
        $orderID = $this->order_model->createNewOrder($userID, $sent_vars['note'], $sent_vars['phone'], $sent_vars['address']);

        $this->shipping_model->create($orderID);

        foreach ($cart as $key => $val) {
            $this->order_model->createOrderDetail($orderID, $val['productID'], $val['unitPrice'], $val["quantity"]);
        }
        $res = $this->order_model->getDetail($orderID);
        $this->ToView($res);
        exit();
    }

    public function myListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];

        $sent_vars = $_GET;

        try {
            $status = str_replace('%20', ' ', $sent_vars['status']);
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->ListOrderByUser($userID, $status, $page, $perPage);
        $this->ToView($res);
        exit();
    }

    public function adminListOrder()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $sent_vars = $_GET;
        try {
            $status = $sent_vars['status'];
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
            $id = empty($sent_vars['orderID']) ?  '' : $sent_vars['orderID'];
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->listOrder($status, $page, $perPage, $startDate, $endDate, $id);
        $this->ToView($res);
        exit();
    }

    public function getMyOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $res = $this->order_model->getDetail($id, 1, $userID);

        $this->ToView($res);
        exit();
    }

    public function adminGetOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res = $this->order_model->getDetail($id, 1);
        $res['obj']['delivery']  = $this->delivery_model->getListByOrder($id);
        $this->ToView($res);
        exit();
    }

    // public function setStatusOrder($id = 0)
    // {
    //     $this->middle_ware->checkRequest('PUT');
    //     $this->middle_ware->adminOnly();
    //     $userID = $_SESSION['user']['ID'];
    //     $order = $this->order_model->getDetail($id, 0);
    //     if (!$order) {
    //         $this->loadErrors(400, 'No orders yet');
    //     }

    //     $json = file_get_contents("php://input");
    //     $sent_vars = json_decode($json, TRUE);

    //     if (empty($sent_vars['status']) || empty($sent_vars['description'])) {
    //         $this->loadErrors(400, 'Not enough value');
    //     }

    //     $description = $sent_vars['description'];

    //     $check = $this->find($sent_vars['status'], status_order);
    //     if (!$check) {
    //         $this->loadErrors(400, 'Value status invalid');
    //     }

    //     $this->order_model->updateStatus($id, $sent_vars['status']);
    //     $this->shipping_model->create($id, $userID, $description);
    //     $res['msg'] = 'Success';
    //     $this->ToView($res);
    //     exit();
    // }

    public function cancelOrder($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();

        $status = 'Canceled';
        try {
            $json = file_get_contents("php://input");
            $sent_vars = json_decode($json, TRUE);
            $userID = $_SESSION['user']['ID'];

            $reason = $sent_vars['reason'];
            $check = $this->find($reason, cancel_reason);
            if ($check === null) {
                $this->loadErrors(400, 'The reason invalid');
            }
            $reason = "Canceled by user: " . $reason;
            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->loadErrors(400, 'No orders yet');
            }
            $order = $order['obj'];
            switch ($order['status']) {
                case status_order[0]:
                    $this->order_model->updateStatus($id, status_order[4]);
                    $this->shipping_model->create($id, $userID, $reason);
                    $res['msg'] = 'Success';
                    $this->ToView($res);
                    exit();
                    break;
                case status_order[1]:
                    $this->loadErrors(400, 'The order is being shipped');
                    exit;
                    break;
                default:
                    $this->loadErrors(400, 'The order has been delivered');
                    exit;
                    break;
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
    public function adminCancelOrder($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $status = 'Canceled';
        try {
            $json = file_get_contents("php://input");
            $sent_vars = json_decode($json, TRUE);
            $userID = $_SESSION['user']['ID'];

            $reason = $sent_vars['reason'];
            $check = $this->find($reason, order_fail);
            if ($check === null) {
                $this->loadErrors(400, 'The reason invalid');
            }

            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->loadErrors(400, 'No orders yet');
            }
            $reason = "Canceled by admin: " . $reason;

            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->loadErrors(400, 'No orders yet');
            }
            $order = $order['obj'];
            switch ($order['status']) {
                case status_order[0]:
                    $this->order_model->updateStatus($id, status_order[4]);
                    $this->shipping_model->create($id, $userID, $reason);
                    $res['msg'] = 'Success';
                    $this->ToView($res);
                    exit();
                    break;
                case status_order[1]:
                    $this->loadErrors(400, 'The order is being shipped');
                    exit;
                    break;
                default:
                    $this->loadErrors(400, 'The order has been delivered');
                    exit;
                    break;
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
    public function orderReceived($id = 0)
    {
        try {
            $this->middle_ware->checkRequest('PUT');
            $this->middle_ware->userOnly();
            $userID = $_SESSION['user']['ID'];

            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->loadErrors(400, 'No orders yet');
                exit();
            }
            $order = $order['obj'];

            switch ($order['status']) {
                case status_order[1]:
                    $this->order_model->updateStatus($id, status_order[2]);
                    $this->shipping_model->create($id, $userID, shipping_status[3]);
                    $res['msg'] = 'Success';
                    $this->ToView($res);
                    exit();
                case status_order[0]:
                    $this->loadErrors(400, 'Orders are being prepared');
                    exit();
                default:
                    $this->loadErrors(400, 'The order has been completed');
                    exit();
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
}