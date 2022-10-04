<?php

require './database/db.php';
require './helper/middleware.php';
checkRequest('GET');
$table = 'product';
$res['status'] = 1;

$page = 1;
$perPage = 10;
$search = '';
$searchType = 'name';
$orderBy = 'name';
$orderType = 'ASC';
$condition = [
    "$searchType" => $search,
];

$offset = $perPage * ($page - 1);

$total = count(selectAll($table, $condition, " ORDER BY $orderBy $orderType "));
$check = ceil($total / $perPage);
if ($page >= $check && $check > 0) {
    $page = $check - 1;
}
$obj = selectAll($table, $condition, " ORDER BY $orderBy $orderType LIMIT $perPage OFFSET $offset");

$totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");

$res['obj'] = $obj;
$res['totalCount'] = $totalCount[0]['totalCount'];
$res['numOfPage'] = ceil($check);
$res['page'] = $page;

dd($res);
exit();