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
    function create($name, $desc)
    {

        $condition = [
            'name' => $name,
            'description' => $desc,
        ];

        $res['msg'] = 'Success';
        create('category', $condition);
        return ($res);
    }
}