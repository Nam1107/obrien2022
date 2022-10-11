<?php
require './database/db.php';
require './helper/middleware.php';

class review
{
    public static function addReview($id)
    {
        checkRequest('POST');
        userOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $userID = $_SESSION['user']['ID'];
        $sent_vars['productID'] = $id;
        $sent_vars['userID'] = $userID;
        $sent_vars['createdAt'] = currentTime();
        $rate = $sent_vars['rate'];
        $check = create('review', $sent_vars);
        $res['status'] = 1;
        custom("
        UPDATE product SET rate = (rate + $rate)/2 WHERE ID = $id
        ");
        $res['msg'] = "Success";
        dd($res);
        exit();
    }

    public static function userReview()
    {
        checkRequest('GET');
        userOnly();
        $userID = $_SESSION['user']['ID'];
    }
}