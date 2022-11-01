<?php

class orderModel
{
    function getDetail($orderID)
    {
        $res['obj'] = selectOne('order', ["ID" => $orderID]);
        $res['obj']['product']  = selectAll('orderDetail', ['orderID' => $orderID]);
        dd($res);
    }
    public function createOrder($userID, $note, $phone, $address)
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
    public function createOrderDetail($orderID, $productID, $unitPrice, $quanity)
    {
        $condition = [
            "orderID" => $orderID,
            "productID" => $productID,
            "unitPrice" => $unitPrice,
            "quanity" => $quanity,
            "createdAt" => currentTime()
        ];
        create('orderDetail', $condition);
    }
}