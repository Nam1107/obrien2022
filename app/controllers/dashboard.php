<?php

class dashboard extends Controllers
{
    public $middle_ware;
    public $dashboard_model;
    public $render_view;
    public function __construct()
    {
        $this->dashboard_model = $this->model('dashboardModel');
        $this->middle_ware = new middleware();
        $this->render_view = $this->render('renderView');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function report()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);


        try {
            $startDate = $sent_vars['startDate'];
            $endDate = $sent_vars['endDate'];
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $report = custom("SELECT A.status,SUM(A.total) AS total,COUNT(A.ID) AS num
        FROM 
        (SELECT `order`.ID,`order`.status,`order`.createdAt,SUM(unitPrice*quantity) AS total
        FROM orderDetail,`order`
        WHERE orderID = `order`.ID
        AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
        GROUP BY orderID) AS A
        GROUP BY A.status
        ");
        $res['report'] = $report;
        $this->render_view->ToView($res);
        exit;
    }
}