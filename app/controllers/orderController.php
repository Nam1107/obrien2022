<?php

class orderController extends Controllers
{
    public $middle_ware;
    public $order_model;
    public $render_view;
    public $shipping_model;
    public function __construct()
    {
        $this->order_model = $this->model('orderModel');
        $this->cart_model = $this->model('cartModel');
        $this->shipping_model = $this->model('shippingModel');
        $this->render_view = $this->render('renderView');
        $this->middle_ware = new middleware();
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }



    function listStatus()
    {
        $this->middle_ware->checkRequest('GET');
        $this->render_view->ToView(status_order);
        exit();
    }

    function cancelReason()
    {
        $this->middle_ware->checkRequest('GET');
        $this->render_view->ToView(cancel_reason);
        exit;
    }

    function orderFail()
    {
        $this->middle_ware->checkRequest('GET');
        $this->render_view->ToView(order_fail);
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
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
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
        $this->render_view->ToView($res);
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
            $this->render_view->loadErrors(400, 'Error: input is invalid');
        }
        $cart = $this->cart_model->getCart($userID)['obj'];
        if (!$cart) {
            $this->render_view->loadErrors(400, 'Your cart is empty');
        }
        foreach ($cart as $key => $val) {
            if ($val['status'] === 0) {
                $this->render_view->loadErrors(400, 'Some items in your cart has sold out');
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
        $this->render_view->ToView($res);
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
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->ListOrderByUser($userID, $status, $page, $perPage);
        $this->render_view->ToView($res);
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
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->order_model->listOrder($status, $page, $perPage, $startDate, $endDate);
        $this->render_view->ToView($res);
        exit();
    }

    public function getMyOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $userID = $_SESSION['user']['ID'];
        $res = $this->order_model->getDetail($id, 1, $userID);
        $this->render_view->ToView($res);
        exit();
    }

    public function adminGetOrder($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res = $this->order_model->getDetail($id, 1);
        $this->render_view->ToView($res);
        exit();
    }

    public function setStatusOrder($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $order = $this->order_model->getDetail($id, 0);
        if (!$order) {
            $this->render_view->loadErrors(400, 'No orders yet');
        }

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        if (empty($sent_vars['status']) || empty($sent_vars['description'])) {
            $this->render_view->loadErrors(400, 'Not enough value');
        }

        $check = $this->find($sent_vars['status'], status_order);
        if (!$check) {
            $this->render_view->loadErrors(400, 'Value status invalid');
        }

        $this->order_model->updateStatus($id, $sent_vars['status'], $sent_vars['description']);
        $res['msg'] = 'Success';
        $this->render_view->ToView($res);
        exit();
    }

    public function cancelOrder($id = 0)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();

        $status = 'Canceled';
        try {
            $json = file_get_contents("php://input");
            $sent_vars = json_decode($json, TRUE);

            $reason = $sent_vars['reason'];
            $reason = "Reason for Cancellation : " . $reason;

            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->render_view->loadErrors(400, 'No orders yet');
            }
            $order = $order['obj'];
            switch ($order['status']) {
                case status_order[0]:
                    $this->order_model->updateStatus($id, status_order[5], $reason);
                    $res['msg'] = 'Success';
                    $this->render_view->ToView($res);
                    exit();
                    break;
                case status_order[1]:
                    $this->render_view->loadErrors(400, 'The order is being shipped');
                    exit;
                    break;
                default:
                    $this->render_view->loadErrors(400, 'The order has been delivered');
                    exit;
                    break;
            }
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
    public function orderReceived($id = 0)
    {
        try {
            $this->middle_ware->checkRequest('PUT');
            $this->middle_ware->userOnly();

            $order = $this->order_model->getDetail($id, 0);
            if (!$order) {
                $this->render_view->loadErrors(400, 'No orders yet');
                exit();
            }
            $order = $order['obj'];

            switch ($order['status']) {
                case status_order[1]:
                    $this->order_model->updateStatus($id, status_order[3], shipping_status[3]);
                    $res['msg'] = 'Success';
                    $this->render_view->ToView($res);
                    exit();
                case status_order[0]:
                    $this->render_view->loadErrors(400, 'Orders are being prepared');
                    exit();
                default:
                    $this->render_view->loadErrors(400, 'The order has been completed');
                    exit();
            }
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
}