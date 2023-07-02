<?php
namespace App\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    protected $user;

    public function __construct()
    {
        $modelUser = new User;
        $this->user = $modelUser;
    }

    public function login()
    {
        session_start();
        $errors = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $remember = isset($_POST['remember']) ? $_POST['remember'] : false;
            
			$auth = $this->user->login($username, $password, $remember);
            if (!$auth) {
                $errors = 'Tài khoản hoặc mật khẩu sai!';
            }
        }

        $request = Request::createFromGlobals();
        if ($_SERVER['REQUEST_METHOD'] === 'GET' &&  $request->query->get('action') == 'logout') { 
            $this->user->logout();
        }

        if($this->user->isLoggedIn()) {
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

            if ($this->user->register($username, $password)) {
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
        $this->user->logout();

        // Chuyển hướng hoặc thực hiện các thao tác khác sau khi đăng xuất
    }

    public function profile()
    {
        if ($this->user->isLoggedIn()) {
            $user = $_SESSION['user'];

            // Hiển thị giao diện thông tin người dùng
        } else {
            // Người dùng chưa đăng nhập, chuyển hướng hoặc thực hiện các thao tác khác
        }
    }
}
?>
