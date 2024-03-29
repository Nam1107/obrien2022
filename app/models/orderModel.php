<?php

class orderModel extends Controllers
{
    function getDetail($orderID, $all = 0, $userID = 0)
    {
        // $order = custom("
        // SELECT `order`.* ,SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        // FROM `order`,`orderDetail`	
        // WHERE `order`.ID = orderDetail.orderID
        // AND `order`.ID = $orderID
        // GROUP BY
        // `orderDetail`.orderID
        // ");
        $orderQuery1 = "SELECT `order`.* ,CAST(SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS FLOAT) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`	
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.ID = $orderID";
        $orderQuery2 = "GROUP BY
        `orderDetail`.orderID";

        $userQuery = " ";
        if ($userID != 0) {
            $userQuery = " AND `order`.userID = $userID  ";
        }

        $order = custom($orderQuery1 . $userQuery . $orderQuery2);


        if (!$order) {
            return null;
        }

        $res['obj'] = $order[0];

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
            $shipping = custom("SELECT shippingDetail.description,shippingDetail.createdAt
            from shippingDetail
            WHERE orderID =  $orderID
            ");

            $product = custom("SELECT product.ID, product.image,product.name,unitPrice,quantity
            FROM `product`,`orderDetail`	
            WHERE `product`.ID = orderDetail.productID
            AND orderID = $orderID
            ");

            $res['obj']['shipping'] = $shipping;
            $res['obj']['product'] = $product;
        }

        return $res;
    }
    function listOrder($status, $page, $perPage, $startDate, $endDate, $id = '')
    {
        $offset = $perPage * ($page - 1);
        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT `order`.ID
                FROM `order`,`orderDetail`,user
                WHERE `order`.status LIKE '%$status%'
                AND user.ID = `order`.userID
                AND `order`.ID = orderDetail.orderID 
                AND `order`.ID like '%$id%'
                AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
                GROUP BY `order`.ID
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.*,user.name ,SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
        FROM `order`,`orderDetail`,user
        WHERE `order`.ID = orderDetail.orderID
        AND `order`.status LIKE '%$status%'
        AND user.ID = `order`.userID
        AND `order`.ID like '%$id%'
        AND `order`.createdAt > '$startDate' AND  `order`.createdAt < '$endDate'
        GROUP BY `orderDetail`.orderID
        ORDER BY `order`.createdAt DESC
        LIMIT $perPage  OFFSET $offset 
        ");
        $res['totalCount'] = (int)$total[0]['total'];
        $res['numOfPage'] = (int)$check;
        $res['page'] = (int)$page;
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
                FROM `order`,`orderDetail`,user
                WHERE `order`.status LIKE '%$status%'
                AND `order`.ID = orderDetail.orderID
                AND `order`.userID = $userID
                GROUP BY `order`.ID
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT `order`.ID,`order`.status ,C.description,C.createdAt AS lastUpdated, `order`.createdAt ,CAST(SUM(`orderDetail`.unitPrice*`orderDetail`.quantity) AS FLOAT ) AS total,  COUNT(`orderDetail`.orderID) AS numOfProduct
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

        foreach ($order as $key => $obj) {
            $val = $obj['ID'];
            $order[$key]['product'] = custom("SELECT product.ID, product.image,product.name,unitPrice,quantity
            FROM `product`,`orderDetail`	
            WHERE `product`.ID = orderDetail.productID
            AND orderID = $val
            ");
        }
        $res['totalCount'] = (int)$total[0]['total'];
        $res['numOfPage'] = (int)$check;
        $res['page'] = (int)$page;
        $res['obj'] = $order;
        return $res;
    }
    public function updateStatus($orderID, $status, $description = '')
    {
        update('order', ['ID' => $orderID], ['status' => $status]);
    }
    public function createNewOrder($userID, $note, $phone, $address)
    {
        $order['userID'] = $userID;
        $order['note'] = $note;
        $order['status'] = status_order[0];
        $order['phone'] = $phone;
        $order['address'] = $address;
        $order['createdAt'] = currentTime();

        $orderID = create('order', $order);
        return $orderID;
    }
    public function createOrderDetail($orderID, $productID, $unitPrice, $quantity)
    {
        $condition = [
            "orderID" => $orderID,
            "productID" => $productID,
            "unitPrice" => $unitPrice,
            "quantity" => $quantity,
            "createdAt" => currentTime()
        ];
        create('orderDetail', $condition);
    }
}