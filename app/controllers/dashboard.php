<?php
require './database/db.php';
require './helper/middleware.php';
class dashboard
{
    public function report()
    {
        checkRequest('GET');
        adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $startDate = $sent_vars['startDate'];
        $endDate = $sent_vars['endDate'];

        $report = custom("SELECT A.status,SUM(A.total) AS total,COUNT(A.ID) AS num
        FROM 
        (SELECT `order`.ID,`order`.status,`order`.createdAt,SUM(unitPrice*quanity) AS total
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