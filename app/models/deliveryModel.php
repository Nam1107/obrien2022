<?php

class deliveryModel extends Controllers
{

    function getDetail($delivery_id, $value = '*')
    {
        $delivery = custom("
                SELECT $value
                FROM delivery_order
                WHERE delivery_order.id = $delivery_id
                ORDER BY id DESC
        ");


        if (empty($delivery)) {
            return null;
        }

        $res = $delivery[0];

        return ($res);
    }

    function getListByStatus($status, $page, $perPage, $startDate, $endDate)
    {
        $offset = $perPage * ($page - 1);
        $total = custom(
            "SELECT COUNT(id) as total
            FROM (
                SELECT id
                FROM `delivery_order`
                WHERE `delivery_order`.status LIKE '%$status%'
                AND `delivery_order`.departed_date > '$startDate' AND  `delivery_order`.departed_date < '$endDate'
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT *
        FROM `delivery_order`
        WHERE `delivery_order`.status LIKE '%$status%'
        AND `delivery_order`.departed_date > '$startDate' AND  `delivery_order`.departed_date < '$endDate'
        ORDER BY `delivery_order`.departed_date DESC
        LIMIT $perPage  OFFSET $offset 
        ");


        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] =  $check;
        $res['page'] = $page;
        $res['obj'] = $order;
        return $res;
    }

    function getListByShipper($shipper_id, $status, $page, $perPage, $startDate, $endDate)
    {
        $offset = $perPage * ($page - 1);
        $total = custom(
            "SELECT COUNT(id) as total
            FROM (
                SELECT id
                FROM `delivery_order`
                WHERE `delivery_order`.status LIKE '%$status%'
                AND delivery_order.shipper_id = $shipper_id
                AND `delivery_order`.departed_date > '$startDate' AND  `delivery_order`.departed_date < '$endDate'
            ) AS B
        "
        );

        $check = ceil($total[0]['total'] / $perPage);

        $order = custom("
        SELECT *
        FROM `delivery_order`
        WHERE `delivery_order`.status LIKE '%$status%'
        AND delivery_order.shipper_id = $shipper_id
        AND `delivery_order`.departed_date > '$startDate' AND  `delivery_order`.departed_date < '$endDate'
        ORDER BY `delivery_order`.departed_date DESC
        LIMIT $perPage  OFFSET $offset 
        ");

        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] =  $check;
        $res['page'] = $page;
        $res['obj'] = $order;
        return $res;
    }



    function getListByOrder($order_id, $value = '*')
    {
        $delivery = custom("
                SELECT $value
                FROM delivery_order
                WHERE delivery_order.order_id = $order_id
                ORDER BY id DESC
        ");
        $res = $delivery;

        return ($res);
    }

    function create($order_id = null, $shipper_id = null)
    {
        $user_id = $_SESSION['user']['id'];
        $sent_vars = [
            'order_id' => $order_id,
            'shipper_id' => $shipper_id,
            'created_by' => $user_id,
            'departed_date' => currentTime(),
            'delivered_date' => null,
            'status' => delivery_status[0],
            'description' => null
        ];
        $delivery_id = create('delivery_order', $sent_vars);

        update('tbl_order', ['id' => $order_id], ['status' => status_order[1]]);

        return $delivery_id;
    }

    function update($delivery_id,  $status, $description, $delivered_date = null)
    {
        $user_id = $_SESSION['user']['id'];
        $delivery = [
            'created_by' => $user_id,
            'status' => $status,
            'description' => $description,
            'delivered_date' => $delivered_date
        ];
        update('delivery_order', ['id' => $delivery_id], $delivery);
    }
}