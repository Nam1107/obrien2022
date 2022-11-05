<?php

class productModel extends Controllers
{
    protected $table = 'product';
    public $model_product;
    public $middle;
    public function __construct()
    {
        $this->middle = new middleware();
    }

    public function checkProduct($id, $IsPublic)
    {
        $pro = selectOne('product', ['ID' => $id, 'IsPublic' => $IsPublic]);
        if (!$pro) {
            $this->loadErrors(404, 'Not found product');
            exit();
        }
    }

    public function getDetail($id, $IsPublic)
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
        if (empty($obj)) {
            $res['status'] = 0;
            $res['errors'] = "Not found product with ID = $id";
            return ($res);
        }

        $wish = custom("
        SELECT *
            FROM wishList
            WHERE userID = $userID
            AND productID = $id
        ");

        if (!$wish) {
            $obj[0]['wishList'] = 0;
        } else $obj[0]['wishList'] = 1;


        $gallery = selectAll('gallery', ['productID' => $id]);
        $res['status'] = 1;
        $obj[0]['gallery'] = $gallery;
        $res['obj'] = $obj[0];

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
            AND IsPublic LIKE '%1%'
            ORDER BY $sortBy $sortType
            LIMIT $perPage OFFSET $offset
            
            
            "
        );
        $res['status'] = 1;
        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = $check;
        $res['page'] = $page;
        $res['obj'] = $obj;
        return $res;
    }

    public function create($sent_vars, $gallery)
    {
        $sent_vars['createdAt'] = currentTime();
        $sent_vars['updatedAt'] = currentTime();

        $id = create('product', $sent_vars);

        if ($id == 0) {
            $res['status'] = 0;
            $res['errors'] = 'Invalid Parameter';
            return $res;
        }

        foreach ($gallery as $key => $url) :
            $image['productID'] = $id;
            $image['URLImage'] =  $url;
            create('gallery', $image);
        endforeach;

        $res['status'] = 1;
        $res['msg'] = 'Success';

        return $res;
    }
    public function delete($id)
    {
        $IsPublic = '';
        $obj = $this->getDetail($id, $IsPublic);
        if ($obj['status'] == 0) {
            return ($obj);
        };
        delete('product', ['ID' => $id]);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        return $res;
    }
    public function update($id, $sent_vars)
    {
        $sent_vars['updatedAt'] = currentTime();

        $res['obj'] = update('product', ['ID' => $id], $sent_vars);
        $res['status'] = 1;
        $res['msg'] = 'Success';
        return ($res);
    }
}