<?php

require './database/db.php';
require './helper/middleware.php';
checkRequest('GET');
$table = 'product';
$res['status'] = 1;
if (!isset($_GET['page']) || $_GET['page'] <= 0) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$perPage = 10;
if (isset($_GET['perPage'])) {
    $perPage = $_GET['perPage'];
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$searchType = 'name';
if (isset($_GET['searchType'])) {
    $searchType = $_GET['searchType'];
}

$orderBy = 'name';
if (isset($_GET['orderBy'])) {
    $orderBy = $_GET['orderBy'];
}

$orderType = 'ASC';
if (isset($_GET['orderType'])) {
    $orderType = $_GET['orderType'];
}

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
$sizeArray = sizeof($obj) - 1;
for ($i = 0; $i <= $sizeArray; $i++) {
    if (currentTime() > $obj["$i"]['startSale'] && currentTime() < $obj["$i"]['endSale']) {
        $obj["$i"]['saleStatus'] = 1;
    } else $obj["$i"]['saleStatus'] = 0;
    $gallery = selectAll('gallery', ['productID' => $obj["$i"]['ID']]);
    $obj["$i"]["gallery"] = $gallery;
    $category = selectOne('category', ['ID' => $obj["$i"]['categoryID']]);
    unset($obj["$i"]['categoryID']);
    $obj["$i"]['category'] = $category;
}
$totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");

$res['obj'] = $obj;
$res['totalCount'] = $totalCount[0]['totalCount'];
$res['numOfPage'] = ceil($check);
$res['page'] = $page;

dd($res);
exit();