<?php

class userModel
{
    protected $table = 'user';
    public function getList($page, $perPage, $email, $sortBy, $sortType, $role = '')
    {
        $offset = $perPage * ($page - 1);
        $total = custom("
        SELECT COUNT(ID) as total
        FROM (
            SELECT user.ID
            FROM `user`,tbl_role
            WHERE email LIKE '%$email%' 
            AND tbl_role.id = user.role
            AND tbl_role.role_name LIKE '%$role%'
            ORDER BY $sortBy $sortType
            ) as B
        ");
        $check = ceil($total[0]['total'] / $perPage);

        $obj = custom("
        SELECT user.ID,email,phone,firstName,lastName,`user`.name,avatar ,tbl_role.role_name AS `role`
        FROM `user`,tbl_role
        WHERE email LIKE '%$email%' 
        AND tbl_role.id = user.role
        AND tbl_role.role_name LIKE '%$role%'
        ORDER BY $sortBy $sortType LIMIT $perPage OFFSET $offset
        ");


        $res['totalCount'] = $total[0]['total'];
        $res['numOfPage'] = ceil($check);
        $res['page'] = (int)$page;
        $res['obj'] = $obj;

        return ($res);
    }

    public function getDetail($id)
    {
        $obj = custom("
        SELECT user.ID,email,phone,firstName,lastName,`user`.name,avatar ,tbl_role.role_name AS `role`
        FROM `user`,tbl_role
        WHERE `user`.id = $id
        AND tbl_role.id = user.role
        ");

        if (!$obj) {
            return null;
        } else {
            $obj = $obj[0];
        }

        return $obj;
    }
    public function findUser($condition)
    {
        $user = selectOne('user', $condition);
        return $user;
    }
    public function createUser($email, $password)
    {
        $input['email'] = $email;
        $input['password'] = $password;
        $input['firstName'] = '';
        $input['lastName'] = '';
        $input['firstName'] = '';
        $input['role'] = '1';
        $input['avatar'] = 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg';
        $input['createdAt'] = currentTime();
        create('user', $input);
    }
}