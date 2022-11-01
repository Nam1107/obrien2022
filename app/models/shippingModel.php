<?php
class shippingModel
{
    function create($orderID)
    {
        $shipping = [
            "orderID" => $orderID,
            "description" => "Order has been created",
            "createdAt" => currentTime()
        ];
        create('shippingDetail', $shipping);
    }
    function setStatus()
    {
    }
}