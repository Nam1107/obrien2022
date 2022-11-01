<?php

class reviewModel
{
    function create($productID, $userID, $rate, $comment)
    {
        $obj['productID'] = $productID;
        $obj['comment'] =  $comment;
        $obj['userID'] = $userID;
        $obj['createdAt'] = currentTime();
        $rate = $obj['rate'] = $rate;
        create('review', $obj);
    }
    function listByProduct($productID, $page, $perPage, $rate)
    {
        $offset = $perPage * ($page - 1);

        $num = custom("SELECT rate, COUNT(ID) AS count from review where productID = $productID GROUP BY rate ASC");

        $total = custom("
        SELECT COUNT(ID) as total
            FROM (
                Select * from review where rate LIKE '%$rate%' and productID = $productID
            ) AS B
        
        ");

        $check = ceil($total[0]['total'] / $perPage);

        $obj = custom("
        SELECT review.*,user.email,user.name,user.avatar 
        FROM review,user
        WHERE rate LIKE '%$rate%'
        AND productID = $productID
        AND review.userID = user.ID
        LIMIT $perPage OFFSET $offset
        ");

        $res['status'] = 1;
        $res['page'] = $page;
        $res['numOfPage'] = $check;
        $res['countOfReviews'] = $num;
        $res['obj'] = $obj;
        return $res;
    }
    function listByUser($userID, $page, $perPage)
    {
        $offset = $perPage * ($page - 1);
        $total = custom("
        SELECT COUNT(ID) as total
            FROM (
                Select * from review where userID = $userID
            ) AS B
        
        ");
        $check = ceil($total[0]['total'] / $perPage);

        $obj = custom("
        Select review.* ,product.name,product.image
        from review,product
        where userID = $userID
        AND product.ID=review.productID
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