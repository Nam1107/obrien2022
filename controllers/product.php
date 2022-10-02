<?php
require './database/db.php';

class Product
{

    function ListProduct()
    {
        $table = 'product';
        if (!isset($_GET['page']) || $_GET['page'] <= 0) {
            $page = 1;
        } else {
            $page = $_GET['page'] - 1;
        }
        $orderBy = 'ASC';
        if (isset($_GET['order-by'])) {
            $orderBy = $_GET['order-by'];
        }
        // $perPage = $_GET['per-page'];
        $perPage = 2;
        $offset = $perPage * ($page - 1);

        $total = count(search($table, [], " ORDER BY createdAt $orderBy"));
        $check = ceil($total / $perPage);
        if ($page >= $check && $check > 0) {
            $page = $check - 1;
        }
        $obj = search($table, [], " ORDER BY createdAt $orderBy LIMIT $perPage OFFSET $offset");
        $TotalCount = custom("SELECT COUNT(*)  AS TotalCount FROM $table");
        $data['Obj'] = $obj;
        $data['TotalCount'] = $TotalCount[0]['TotalCount'];
        $data['NumOfPage'] = ceil($check);
        $data['Page'] = $page;
        dd($data);
        exit();
    }
}