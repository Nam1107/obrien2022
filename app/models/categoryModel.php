<?php

class categoryModel
{
    function getDetail($cate)
    {
        $category =  custom("
                SELECT * FROM category WHERE name LIKE '%$cate%' 
            ");
        if (!$category) {
            return null;
        } else {
            return $category[0];
        }
    }
    function getList()
    {

        $res['msg'] = 'Success';
        $obj = custom("
            SELECT * from category
        ");
        $res['obj'] = $obj;
        return ($res);
    }
}