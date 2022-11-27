<?php

class orderModel extends Controllers
{
    function getDetail($orderID, $all = 0)
    {
        $order = custom("
        SELECT `order`.* ,SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.ID = $orderID
        GROUP BY
        `orderDetail`.orderID
        ");

        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
        }

        $res['obj'] = $order[0];


        $shipping = custom("SELECT shippingDetail.description,shippingDetail.createdAt
        from shippingDetail
        WHERE orderID =  $orderID
        ");

        $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quantity
        FROM `product`,`orderDetail`	
        WHERE `product`.ID = orderDetail.productID
        AND orderID = $orderID
        ");


        if ($all == 1) {
            $user = custom("
                SELECT `user`.*
                FROM `user`,`order`
                WHERE order.ID = $orderID
                AND user.ID = `order`.userID
            ");
            if (!$user) {
                $res['obj']['user'] = null;
            } else {
                $res['obj']['user'] = $user[0];
            }
        }
        $res['obj']['shipping'] = $shipping;
        $res['obj']['product'] = $product;
        return $res;
    }
    function listOrder($status, $page, $perPage, $startDate, $endDate)
    {
        $offset = $perPage * ($page - 1);
        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT `order`.ID
                FROM `order`
                WHERE `order`.status LIKE '%$status%'
                AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.ID,`order`.userID,user.name,`order`.status , `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`,user
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.status LIKE '%$status%'
        AND user.ID = `order`.userID
        AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
        GROUP BY `orderDetail`.orderID
        ORDER BY `order`.createdAt DESC
        LIMIT $perPage  OFFSET $offset 
        ");
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = $page;
        $res['obj'] = $order;
        // $res = $this->loadList($total[0]['total'], $check, $page, $order);
        return $res;
    }
    function ListOrderByUser($userID, $status, $page, $perPage)
    {
        $offset = $perPage * ($page - 1);

        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT `order`.ID
                FROM `order`
                WHERE `order`.status LIKE '%$status%'
                AND `order`.userID = $userID
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.ID,`order`.status ,C.description,C.createdAt AS lastUpdated, `order`.createdAt ,SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	,(
        SELECT shippingDetail.*
        FROM (SELECT max(ID) AS curID
        from shippingDetail
        group by orderID) AS B, shippingDetail
        WHERE curID = ID
        ) AS C
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.userID = $userID
        AND `order`.status like '%$status%'
        AND C.orderID = `order`.ID
        GROUP BY
        `orderDetail`.orderID
        LIMIT $perPage  OFFSET $offset 
        ");

        if (!$order) {
            $this->loadErrors(400, 'No orders yet');
        }

        foreach ($order as $key => $obj) {
            $val = $obj['ID'];
            $order[$key]['product'] = custom("SELECT product.ID, product.image,product.name,unitPrice,quantity
            FROM `product`,`orderDetail`	
            WHERE `product`.ID = orderDetail.productID
            AND orderID = $val
            ");
        }
        $res['status'] = 1;
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = (int)$page;
        $res['obj'] = $order;
        return $res;
    }
}