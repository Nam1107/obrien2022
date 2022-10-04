<?php
// require './database/db.php';
// require './helper/middleware.php';



function test()
{
    checkRequest('GET');
    $table = 'product';
    // $res['status'] = 1;
    // if (!isset($_GET['page']) || $_GET['page'] <= 0) {
    //     $page = 1;
    // } else {
    //     $page = $_GET['page'];
    // }

    // $perPage = 10;
    // if (isset($_GET['perPage'])) {
    //     $perPage = $_GET['perPage'];
    // }

    // $search = '';
    // if (isset($_GET['search'])) {
    //     $search = $_GET['search'];
    // }

    // $searchType = 'name';
    // if (isset($_GET['searchType'])) {
    //     $searchType = $_GET['searchType'];
    // }

    // $orderBy = 'name';
    // if (isset($_GET['orderBy'])) {
    //     $orderBy = $_GET['orderBy'];
    // }

    // $orderType = 'ASC';
    // if (isset($_GET['orderType'])) {
    //     $orderType = $_GET['orderType'];
    // }

    // $condition = [
    //     "$searchType" => $search,
    // ];

    // $offset = $perPage * ($page - 1);

    $obj = selectAll($table);

    // $product['condition'] = $condition;
    $product['pro'] = $obj;
    $product['obj'] = custom('select * from product');
    dd($product);
    exit;
}

function ListProduct()
{
    checkRequest('GET');
    $table = 'product';
    $res['status'] = 1;
    if (!isset($_GET['page']) || $_GET['page'] <= 0) {
        $page = 1;
    } else {
        $page = $_GET['page'];
    }

    $perPage = 10;
    if (isset($_GET['perPage'])) {
        $perPage = $_GET['perPage'];
    }

    $search = '';
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }

    $searchType = 'name';
    if (isset($_GET['searchType'])) {
        $searchType = $_GET['searchType'];
    }

    $orderBy = 'name';
    if (isset($_GET['orderBy'])) {
        $orderBy = $_GET['orderBy'];
    }

    $orderType = 'ASC';
    if (isset($_GET['orderType'])) {
        $orderType = $_GET['orderType'];
    }

    $condition = [
        "$searchType" => $search,
    ];

    $offset = $perPage * ($page - 1);

    $total = count(selectAll($table, $condition, " ORDER BY $orderBy $orderType "));
    $check = ceil($total / $perPage);
    if ($page >= $check && $check > 0) {
        $page = $check - 1;
    }
    $obj = selectAll($table, $condition, " ORDER BY $orderBy $orderType LIMIT $perPage OFFSET $offset");
    // $sizeArray = sizeof($obj) - 1;
    // for ($i = 0; $i <= $sizeArray; $i++) {
    //     if (currentTime() > $obj["$i"]['startSale'] && currentTime() < $obj["$i"]['endSale']) {
    //         $obj["$i"]['saleStatus'] = 1;
    //     } else $obj["$i"]['saleStatus'] = 0;
    //     $gallery = selectAll('gallery', ['productID' => $obj["$i"]['ID']]);
    //     $obj["$i"]["gallery"] = $gallery;
    //     $category = selectOne('category', ['ID' => $obj["$i"]['categoryID']]);
    //     unset($obj["$i"]['categoryID']);
    //     $obj["$i"]['category'] = $category;
    // }
    $totalCount = custom("SELECT COUNT(*)  AS totalCount FROM $table");

    $res['obj'] = $obj;
    $res['totalCount'] = $totalCount[0]['totalCount'];
    $res['numOfPage'] = ceil($check);
    $res['page'] = $page;

    dd($res);
    exit();
}
function getProduct()
{
    checkRequest('GET');
    $table = 'product';
    $res['status'] = 0; // 1: success; 0: failed;
    $res['status'] = 1;
    $obj = selectOne($table, ['ID' => $_GET['ID']]);
    if (currentTime() > $obj['startSale'] && currentTime() < $obj['endSale']) {
        $obj['saleStatus'] = 1;
    } else $obj['saleStatus'] = 0;
    $res['obj'] = $obj;
    dd($res);
    exit();
}

function createProduct()
{
    checkRequest('POST');
    adminOnly();
    $table = 'product';
    $res['status'] = 0;
    $_POST['createdAt'] = currentTime();
    $_POST['updatedAt'] = currentTime();
    $productID = create($table, $_POST);
    if (!$productID) {
        $res['status'] = 0;
        $res['msg'] = 'Errors';
    } else {
        $res['status'] = 1;
        $res['msg'] = 'Success';
        $res['obj'] = selectOne($table, ['ID' => $productID]);
    }

    dd($res);
    exit();
}

function deleteProduct()
{
    checkRequest('DELETE');
    $table = 'product';
    adminOnly();
    parse_str(file_get_contents("php://input"), $sent_vars);
    if (isset($sent_vars['ID'])) {
        $id['ID'] = $sent_vars['ID'];
        $status = delete($table, $id);
        if ($status == 1) {
            $res['status'] = 1;
            $res['msg'] = 'Success';
            dd($res);
            exit();
        }


        $res['status'] = 0;
        $res['msg'] = 'Not found product by ID';

        dd($res);
        exit();
    }
}
function updateProduct()
{
    checkRequest('PUT');
    $table = 'product';
    adminOnly();

    parse_str(file_get_contents("php://input"), $sent_vars);
    $id['ID'] =  $sent_vars['ID'];
    $sent_vars['updatedAt'] = currentTime();
    unset($sent_vars['ID']);
    $res['status'] = 1;
    $res['msg'] = update($table, $id, $sent_vars);
    dd($res);
    exit();
}