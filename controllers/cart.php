<?php
require './database/db.php';
require './helper/middleware.php';
class cart
{
    function product()
    {
        checkRequest('GET');
        $product = custom('select * from product');
        dd($product);
        exit;
    }
    function user()
    {
        checkRequest('GET');
        $product = custom('select * from user');

        dd($product);
        exit;
    }
}