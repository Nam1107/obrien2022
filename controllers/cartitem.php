<?php

require './database/db.php';
require './helper/middleware.php';
checkRequest('GET');
$obj['product'] = custom('select * from product');
$obj['user'] = custom('select * from user');
dd($obj);
exit;