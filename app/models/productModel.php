<?php

class productModel extends Controllers
{
    protected $table = 'product';
    public $middle;
    public function __construct()
    {
        $this->middle = new middleware();
    }



    public function getDetail($id, $IsPublic = '')
    {
        $userID = 0;
        $obj = $this->middle->authenToken();
        if ($obj['status'] == 1) {
            $userID = $_SESSION['user']['ID'];
        }

        $obj = custom("
            SELECT A.* , category.name AS category
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND A.IsPublic like '%$IsPublic%'
            AND A.ID = $id
        
        ");

        if (!$obj) {
            return null;
        } else {
            $obj = $obj[0];
        }

        $wish = custom("
        SELECT *
            FROM wishList
            WHERE userID = $userID
            AND productID = $id
        ");

        if (!$wish) {
            $obj['wishList'] = 0;
        } else $obj['wishList'] = 1;


        $gallery = selectAll('gallery', ['productID' => $id]);

        $obj['gallery'] = $gallery;
        $res['obj'] = $obj;

        return ($res);
    }

    public function getList($page, $perPage, $name, $category, $IsPublic, $sale, $sortBy, $sortType)
    {
        $offset = $perPage * ($page - 1);

        $total = custom(
            "SELECT COUNT(ID) as total
            FROM (
                SELECT A.* , category.name AS category
                FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
                FROM product) AS A,category
                WHERE A.categoryID = category.ID
                AND category.name LIKE '%$category%'
                AND A.name LIKE '%$name%'
                AND statusSale LIKE '%$sale%'
                AND IsPublic LIKE '%$IsPublic%'
            ) AS B
        "
        );
        $check = ceil($total[0]['total'] / $perPage);
        $obj = custom(
            "SELECT A.* , category.name AS category, IF(A.statusSale = '1', A.priceSale, A.price) AS curPrice
            FROM (SELECT *, IF(startSale<NOW() && endSale>NOW(), '1', '0') AS statusSale
            FROM product) AS A,category
            WHERE A.categoryID = category.ID
            AND category.name LIKE '%$category%'
            AND A.name LIKE '%$name%'
            AND statusSale LIKE '%$sale%'
            AND IsPublic LIKE '%$IsPublic%'
            ORDER BY $sortBy $sortType
            LIMIT $perPage OFFSET $offset
            "
        );

        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = (int)$page;
        $res['obj'] = $obj;
        return $res;
    }
}