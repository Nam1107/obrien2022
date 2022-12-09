<?php
class shippingModel
{
    function create($orderID, $userID = 0, $description)
    {
        $shipping = [
            "orderID" => $orderID,
            "description" => $description,
            "createdAt" => currentTime()
            // "createdBy" => $userID
        ];
        create('shippingDetail', $shipping);
    }
    function getList($orderID)
    {
        $shipping = custom("SELECT shippingDetail.description,shippingDetail.createdAt
        from shippingDetail
        WHERE orderID =  $orderID
        ");
        return $shipping;
    }
}