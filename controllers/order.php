<?php
require './controllers/cart.php';

class order
{

    public static function createOder()
    {
        # code...
        checkRequest('POST');
        adminOnly();
    }
}