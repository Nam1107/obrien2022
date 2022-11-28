<?php

class UserController extends Controllers
{
    public $middle_ware;
    public $user_model;
    public $render_view;
    public function __construct()
    {
        $this->user_model = $this->model('userModel');
        $this->middle_ware = new middleware();
        $this->render_view = $this->render('renderView');
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }
    public function listRole()
    {
        $this->middle_ware->checkRequest('GET');
        $role = custom("SELECT role_name as `role` from tbl_role");
        $role = array_column($role, 'role');
        $res['role'] =  $role;
        $this->render_view->ToView($res);
        exit;
    }
    public function ListUser()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        // $json = file_get_contents("php://input");
        // $sent_vars = json_decode($json, TRUE);
        $sent_vars = $_GET;
        try {
            $page = $sent_vars['page'];
            $perPage = $sent_vars['perPage'];
            $email = $sent_vars['email'];
            $sortBy = $sent_vars['sortBy'];
            $sortType = $sent_vars['sortType'];
            $role = $sent_vars['role'];
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        $res = $this->user_model->getList($page, $perPage, $email, $sortBy, $sortType, $role);
        $this->render_view->ToView($res);
        exit();
    }

    public function getProfile()
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->userOnly();

        $id = $_SESSION['user']['ID'];
        $obj = $this->user_model->getDetail($id);
        $res['obj'] = $obj;
        $this->render_view->ToView($res);
        exit();
    }

    public function getUser($id = 0)
    {
        $this->middle_ware->checkRequest('GET');
        $this->middle_ware->adminOnly();
        $obj = $this->user_model->getDetail($id);
        $res['obj'] = $obj;
        $this->render_view->ToView($res);
        exit();
    }

    public function deleteUser($id = 0)
    {
        $this->middle_ware->checkRequest('DELETE');
        $this->middle_ware->adminOnly();
        $table = 'user';
        $user = $this->user_model->getDetail($id, 'id');
        if (!$user) {
            $this->render_view->loadErrors(404, 'Not found user by ID');
        }
        if ($id == $_SESSION['user']['ID']) {
            $this->render_view->loadErrors(400, 'You cannot delete your account');
        } else {
            delete('user', ['ID' => $id]);
        }
        $res['msg'] = 'Success';
        $this->render_view->ToView($res);
        exit();
    }

    public function updateProfile()
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->userOnly();
        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $id = $_SESSION['user']['ID'];
            $input['phone'] = $sent_vars['phone'];
            $input['firstName'] = $sent_vars['firstName'];
            $input['lastName'] = $sent_vars['lastName'];
            $input['name'] =  $input['firstName'] . " " . $input['lastName'];
            $input['avatar'] = $sent_vars['avatar'];
            $input['updatedAt'] = currentTime();
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        update('user', ['ID' => $id], $input);

        $res['msg'] = 'Success';

        $this->render_view->ToView($res);
        exit();
    }

    public function updateUser($id)
    {
        $this->middle_ware->checkRequest('PUT');
        $this->middle_ware->adminOnly();

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        $sent_vars['updatedAt'] = currentTime();
        unset($sent_vars['email'], $sent_vars['password'], $sent_vars['ID']);

        try {
            $input['phone'] = $sent_vars['phone'];
            $input['firstName'] = $sent_vars['firstName'];
            $input['lastName'] = $sent_vars['lastName'];
            $input['name'] =  $input['firstName'] . " " . $input['lastName'];
            $input['role'] = $sent_vars['role'];
            $input['avatar'] = $sent_vars['avatar'];
            $input['updatedAt'] = currentTime();
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        update('user', ['ID' => $id], $input);
        $res['msg'] = 'Success';
        $this->render_view->ToView($res);
        exit();
    }

    public function setPassword($id)
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->adminOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        try {
            $sent_vars['password'] = password_hash($sent_vars['password'], PASSWORD_DEFAULT);

            $var['updatedAt'] = currentTime();
            $var['password'] = $sent_vars['password'];
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        update('user', ['ID' => $id], $var);
        $res['msg'] = 'Success';
        $this->render_view->ToView($res);
        exit();
    }

    public function changePassword()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->userOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);
        try {
            $errors = validateChangePass($sent_vars);
            if (count($errors) === 0) {
                $sent_vars['newPass'] = password_hash($sent_vars['newPass'], PASSWORD_DEFAULT);
                $id = $_SESSION['user']['ID'];
                $var['updatedAt'] = currentTime();
                $var['password'] = $sent_vars['newPass'];

                update('user', ['ID' => $id], $var);
                $res['msg'] = 'Success';
                $this->render_view->ToView($res);
                exit();
            } else {
                $this->render_view->loadErrors(400, $errors);
            }
        } catch (ErrorException $e) {
            $this->render_view->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
    }
}