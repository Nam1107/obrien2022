<?php
require './database/db.php';
require './helper/middleware.php';

class review
{
    public static function addReview($id)
    {
        checkRequest('POST');
        userOnly();

        $order = selectOne('order', ['ID' => $id]);
        if (!$order) {
            $res['status'] = 0;
            $res['errors'] = ' No orders yet';
            dd($res);
            exit();
        } elseif ($order['status'] != 'To Rate') {
            $res['status'] = 0;
            $res['errors'] = 'Rating is not activated';
            dd($res);
            exit();
        }

        update('order', ['ID' => $id], ['status' => 'Completed']);

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        $userID = $_SESSION['user']['ID'];

        foreach ($sent_vars['review'] as $key => $val) {
            $proID = $obj['productID'] = $val['productID'];
            $obj['comment'] = $val['comment'];
            $obj['userName'] = $val['userName'];
            $obj['userID'] = $userID;
            $obj['createdAt'] = currentTime();
            $rate = $obj['rate'] = $val['rate'];
            create('review', $obj);
            custom("
            UPDATE product SET rate = IF(rate = 0,$rate,(rate + $rate)/2), numOfReviews = (numOfReviews + 1) WHERE ID = $proID
            ");
        }
        $res['status'] = 1;

        $res['msg'] = "Success";
        dd($res);
        exit();
    }

    public static function productReview($id)
    {
        checkRequest('GET');
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $rate = $sent_vars['rate'];
        // $rate = $sent_vars['rate'];
        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $offset = $perPage * ($page - 1);

        $num = custom("SELECT rate, COUNT(ID) AS count from review where productID = 1 GROUP BY rate ASC");
        $total = custom("
        SELECT COUNT(ID) as total
            FROM (
                Select * from review where rate LIKE '%$rate%' and productID = $id
            ) AS B
        
        ");
        $check = ceil($total[0]['total'] / $perPage);
        $obj = custom("
        Select * from review where rate LIKE '%$rate%' and productID = $id
        LIMIT $perPage OFFSET $offset
        ");

        $res['status'] = 1;
        $res['page'] = $page;
        $res['numOfPage'] = $check;
        $res['obj'] = $obj;
        $res['countOfReviews'] = $num;
        dd($res);
        exit();
    }

    public static function myReview()
    {
        checkRequest('GET');
        userOnly();
        $userID = $_SESSION['user']['ID'];

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = $sent_vars['page'];
        $perPage = $sent_vars['perPage'];
        $offset = $perPage * ($page - 1);

        $total = custom("
        SELECT COUNT(ID) as total
            FROM (
                Select * from review where userID = $userID
            ) AS B
        
        ");
        $check = ceil($total[0]['total'] / $perPage);

        $obj = custom("
        Select * from review where userID = $userID
        LIMIT $perPage OFFSET $offset
        ");

        $res['status'] = 1;
        $res['count'] = $total[0]['total'];
        $res['page'] = $page;
        $res['numOfPage'] = $check;
        $res['obj'] = $obj;
        dd($res);
        exit();
    }
}