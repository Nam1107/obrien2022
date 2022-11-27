<?php
require_once './src/JWT.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

use Firebase\JWT\JWT;

class AuthController extends Controllers
{
    public $validate_user;
    public $middle_ware;
    public $wishlist_model;
    public function __construct()
    {
        // $this->wishlist_model = $this->model('categoryModel');
        $this->middle_ware = new middleware();
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING);
    }

    public function Logout()
    {
        $this->middle_ware->checkRequest('POST');
        session_destroy();
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            $this->loadErrors(400, 'You need a token to access');
        }
        $data = $headers['Authorization'];
        $check = explode(" ", $data);

        $token['token'] = $check[1];

        $count = delete('login_token', $token);
        // setcookie('token', '', time() - 7 * 24 * 60 * 60, '/');
        if ($count == 1) {
            $res['msg'] = 'You have successfully logout';
            dd($res);
            exit();
        } else {
            $this->loadErrors(400, 'Refresh token has expired. You must login again');
        }
    }

    public function Login()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->guestsOnly();
        $key = 'privatekey';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $input['email'] = $sent_vars['email'];
            $input['password'] = $sent_vars['password'];
            $errors = validateLogin($input);
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }

        if (count($errors) === 0) {

            $user = selectOne('user', ['email' => $sent_vars['email']]);

            if (!$user) {
                array_push($errors, 'Email address does not exist');
            } elseif (password_verify($sent_vars['password'], $user['password'])) {

                $id = $sessionUser['ID'] = $user['ID'];
                $sessionUser['role'] = $user['role'];

                $payload = [
                    'iss' => 'obrien',
                    'exp' => time() + 60,
                    'data' => [
                        'id' => $id
                    ]
                ];


                $token = JWT::encode($payload, TOKEN_SECRET, 'HS256');

                $login_token['token'] = $token;
                $login_token['userID'] = $user['ID'];
                $login_token['createdAt'] = currentTime();
                // setcookie('token', $login_token['token'], time() + 7 * 24 * 60 * 60, '/');
                create('login_token', $login_token);
                $res['token'] = $token;
                $res['refreshToken'] = $token;

                dd($res);
                exit();
            } else {
                array_push($errors, 'Wrong password');
            }
        }
        $this->loadErrors(400, $errors);
    }
    public function refreshToken()
    {
        $this->middle_ware->checkRequest('GET');
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            $this->loadErrors(400, 'You need a refresh token to access');
        }
        $check = $headers['Authorization'];
        $data = explode(" ", $check);
        $token = $data[1];

        $result = custom("select user.* from user, login_token where user.ID = login_token.userID and login_token.token ='$token'");

        if ($result != null && count($result) > 0) {
            $id = $result[0]['ID'];
            $email = $result[0]['email'];
            $role = $result[0]['role'];
            $payload = [
                'iss' => 'obrien',
                'exp' => time() + 60,
                'data' => [
                    'id' => $id
                ],
            ];
            $token = JWT::encode($payload, TOKEN_SECRET, 'HS256');
            $res['token'] = $token;
            dd($res);
            exit();
        } else {
            $this->loadErrors(400, 'Refresh token not invalid. You must login again');
        }
    }
    public function forgotPassword()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->guestsOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $input['email'] = $sent_vars['email'];
            $input['password'] = $this->rand_string(8);
            $errors = validateForgotPassword($input);

            $user = selectOne('user', ['email' => $sent_vars['email']]);

            if (!$user) {
                array_push($errors, 'Email address does not exist');
            }

            $id = $user['ID'];
            if (count($errors) === 0) {
                $this->sendEmail($input['email'], $input['password']);
                $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            } else {
                $this->loadErrors(400, $errors);
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        update($table, ['ID' => $id], $input);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
    public function Register()
    {
        $this->middle_ware->checkRequest('POST');
        $this->middle_ware->guestsOnly();
        $table = 'user';

        $json = file_get_contents("php://input");
        $sent_vars = json_decode($json, TRUE);

        try {
            $input['email'] = $sent_vars['email'];
            $input['password'] = $this->rand_string(8);
            $errors = validateRegister($input);
            if (count($errors) === 0) {
                $this->sendEmail($input['email'], $input['password']);
                $input['firstName'] = '';
                $input['lastName'] = '';
                $input['firstName'] = '';
                $input['role'] = '1';
                $input['avatar'] = 'https://staticfvvn.s3-ap-southeast-1.amazonaws.com/fv4uploads/uploads/users/4x/6gl/xtq/avatar/thumb_694526497374699.jpg';
                $input['createdAt'] = currentTime();
                $input['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            } else {
                $this->loadErrors(400, $errors);
            }
        } catch (ErrorException $e) {
            $this->loadErrors(400, $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getfile());
        }
        create($table, $input);
        $res['msg'] = 'Success';
        dd($res);
        exit();
    }
    function rand_string($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, $length);
    }
    function sendEmail($email, $password)
    {
        $mail  = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'htrannamss@gmail.com';
        $mail->Password = 'ncchbnjlarsflytl';
        $mail->SMTPSecure = 'ss1';
        $mail->Port = 587;

        $mail->setFrom('htrannamss@gmail.com');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "From Obrien with love";
        $mail->Body = "Your password: " . $password;

        $mail->send();
    }
}