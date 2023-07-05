<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use App\Models\User;

class AuthController
{
    protected $userModel;
    protected $request;

    public function __construct()
    {
      $modelUser = new User;
      $this->userModel = $modelUser;
      $this->request = Request::createFromGlobals();
    }

    public function login()
    {
        session_start();
        $errors = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $remember = isset($_POST['remember']) ? $_POST['remember'] : false;
			      $auth = $this->userModel->login($username, $password, $remember);
            if (!$auth) {
                $errors = 'Tài khoản hoặc mật khẩu sai!';
            }
        }

        $request = Request::createFromGlobals();
        if ($_SERVER['REQUEST_METHOD'] === 'GET' &&  $request->query->get('action') == 'logout') { 
            $this->userModel->logout();
        }

        if($this->userModel->isLoggedIn()) {
            header("Location: ".SITE_URL);
	        exit;
        }else {
            require_once URL_VIEWS_ADMIN . '/auth/login.php';
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->userModel->register($username, $password)) {
                // Đăng ký thành công
                // Chuyển hướng hoặc thực hiện các thao tác khác
            } else {
                // Đăng ký thất bại
                // Hiển thị thông báo lỗi hoặc thực hiện các thao tác khác
            }
        }

        // Hiển thị giao diện đăng ký
    }

    public function logout()
    {
        $this->userModel->logout();
    }
}