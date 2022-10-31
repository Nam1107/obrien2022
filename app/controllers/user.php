<?php

class User extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $user_model;
    public function __construct()
    {
        $this->user_model = $this->model('userModel');
        $this->middle_ware = new middleware();
    }
    public function ListUser()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $page = !empty($sent_vars['page']) ? $sent_vars['page'] : 1;
        $perPage = !empty($sent_vars['perPage']) ? $sent_vars['perPage'] : 10;
        $email = !empty($sent_vars['email']) ? $sent_vars['email'] : '';
        $sortBy = !empty($sent_vars['sortBy']) ? $sent_vars['sortBy'] : 'name';
        $sortType = !empty($sent_vars['sortType']) ? $sent_vars['sortType'] : 'ASC';

        $res = $this->user_model->getList($page, $perPage, $email, $sortBy, $sortType);
        dd($res);
        exit();
    }

    public function getProfile()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();
        $id = $_SESSION['user']['ID'];
        $res = $this->user_model->getDetail($id);
        dd($res);
        exit();
    }

    public function getUser($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $res = $this->user_model->getDetail($id);
        dd($res);
        exit();
    }

    public function deleteUser($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();
        $table = 'user';
        if ($id == $_SESSION['user']['ID']) {
            http_response_code(404);
            $res['status'] = 0;
            $res['errors'] = 'You cannot delete your account';
        } else {
            $res = $this->user_model->delete($id);
        }

        dd($res);
        exit();
    }

    public function updateProfile()
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);


        $id['ID'] = $_SESSION['user']['ID'];
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID'],  $sent_vars['role']);
        $res = $this->user_model->update($id, $sent_vars);

        dd($res);
        exit();
    }

    public function updateUser($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $userID['ID'] =  $id;
        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID']);

        $res = $this->user_model->update($id, $sent_vars);
        dd($res);
        exit();
    }

    public function setPassword($id)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $sent_vars['password'] = password_hash($sent_vars['password'], PASSWORD_DEFAULT);

        $var['updatedAt'] = currentTime();
        $var['password'] = $sent_vars['password'];

        $res = $this->user_model->update($id, $var);
        dd($res);
        exit();
    }

    public function changePassword()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $errors = validateChangePass($sent_vars);
        if (count($errors) === 0) {
            $sent_vars['newPass'] = password_hash($sent_vars['newPass'], PASSWORD_DEFAULT);
            $id = $_SESSION['user']['ID'];
            unset($sent_vars['confirmPass'], $sent_vars['ID']);

            $var['updatedAt'] = currentTime();
            $var['password'] = $sent_vars['newPass'];

            $res = $this->user_model->update($id, $var);
            dd($res);
            exit();
        } else {
            $res['status'] = 0;
            $res['msg'] = $errors;
            dd($res);
            exit();
        }
    }
}