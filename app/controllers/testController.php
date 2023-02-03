<?php

class testController extends Controllers
{
    function test()
    {
        $orderQuery1 = custom("SELECT   CAST(SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS FLOAT) AS total
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID");
        $this->ToView($orderQuery1);
        exit;
    }
}