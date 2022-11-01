<?php

class orderModel
{
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