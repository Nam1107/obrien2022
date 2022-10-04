<?php

require './database/db.php';
require './helper/middleware.php';

checkRequest('GET');
$table = 'user';
$total = count(selectAll($table, [], " ORDER BY ID DESC "));
$check = ceil($total / 10);
$obj = selectAll($table, [], " ORDER BY ID DESC LIMIT 10 OFFSET 0");

$totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");
$res['obj'] = $obj;
$res['totalCount'] = $totalCount[0]['totalCount'];
$res['numOfPage'] = ceil($check);
$res['page'] = $page;
dd($res);
exit;