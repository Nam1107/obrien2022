<?php

class dashboard extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $wishlist_model;
    public function __construct()
    {
        $this->wishlist_model = $this->model('dashboardModel');
        $this->middle_ware = new middleware();
    }
    public function report()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $startDate = !empty($sent_vars['startDate']) ? $sent_vars['startDate'] : '2000-01-01';
        $endDate = !empty($sent_vars['endDate']) ? $sent_vars['endDate'] : '2099-01-01';

        $report = custom("SELECT A.status,SUM(A.total) AS total,COUNT(A.ID) AS num
        FROM 
        (SELECT `order`.ID,`order`.status,`order`.createdAt,SUM(unitPrice*quantity) AS total
        FROM orderDetail,`order`
        WHERE orderID = `order`.ID
        AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
        GROUP BY orderID) AS A
        GROUP BY A.status
        ");
        $res['status'] = 1;
        $res['report'] = $report;
        dd($res);
        exit;
    }
}