<?php

require './database/db.php';
require './helper/middleware.php';

checkRequest('GET');
$table = 'user';

$page = 1;
$perPage = 10;
$search = '';
$searchType = 'name';
$orderBy = 'name';
$orderType = 'ASC';
$offset = $perPage * ($page - 1);
$condition = [
    "$searchType" => $search,
];
$total = count(selectAll($table));
$check = ceil($total / 10);
$obj = selectAll($table, [], " ORDER BY $orderBy $orderType LIMIT $perPage OFFSET $offset");
$totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");
$res['obj'] = $obj;
$res['totalCount'] = $totalCount[0]['totalCount'];
$res['numOfPage'] = ceil($check);
$res['page'] = $page;
dd($res);
exit;