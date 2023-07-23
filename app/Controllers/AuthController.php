<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Middleware\CSRFTokenTrait;
use App\Models\Auth;
use App\Models\User;

class AuthController
{
    use FlashMessage;
    use CSRFTokenTrait;

    protected $userModel;
    protected $request;
    protected $authModel;

    public function __construct()
    {
        $authModel = new Auth;
        $this->authModel = $authModel;
        $this->request = Request::createFromGlobals();

        $userModel = new User;
        $this->userModel = $userModel;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->request->get('username');
            $password = $this->request->get('password');
            $remember = $this->request->get('remember');
            $csrf_login_token = $this->request->get('csrf_login_token');

            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_login_token, 'csrf_login_token');

            if (!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error', 'verifyCSRFToken. Cập nhật thất bại.');
            }


            if ($this->authModel->authenticate($username, $password)) {

                $this->setUserSession($username);

                // Thiết lập remember token khi đăng nhập thành công và tùy chọn Remember Me được chọn
                if ($remember) {
                    $token = $this->generateRememberToken();
                    $this->setRememberToken($token);
                    $this->authModel->saveRememberTokenToDatabase($username, $token);
                }
            } else {
                FlashMessage::error('Tài khoản hoặc mật khẩu sai!');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        $request = Request::createFromGlobals();
        if ($_SERVER['REQUEST_METHOD'] === 'GET' &&  $request->query->get('action') == 'logout') {
            $this->logout();
        }

        if ($this->isLoggedIn()) {
            header("Location: " . SITE_URL);
            exit;
        }

        View::render('admin/auth/login');
    }

    public function register()
    {
        // Incoming
    }

    public function logout()
    {
        $this->unsetUserSession();
        $this->clearRememberToken();
    }

    protected function setUserSession($username)
    {

        $_SESSION['user'] = $this->userModel->getUserByUsername($username);
    }

    protected function unsetUserSession()
    {
        unset($_SESSION['user']);
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
    public function checkUserLogin()
    {
        if (!$this->isLoggedIn() && !$this->checkRememberToken()) {
            header("Location: login");
            exit;
        }
    }
    public function checkRoleAdmin($urlRedirect = SITE_URL)
    {
        if (!$this->isRoleAdmin()) {
            // Xử lý khi người dùng không có quyền admin
            FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
            header("Location: " . $urlRedirect);
            exit;
        }
    }

    public function setRememberToken($token)
    {
        setcookie('remember_token', $token, time() + 604800, '/');
    }

    public function getRememberToken()
    {
        return $_COOKIE['remember_token'] ?? null;
    }

    public function clearRememberToken()
    {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    public function generateRememberToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    // Phương thức phân quyền dựa trên vai trò của người dùng
    public function checkRole($allowedRoles)
    {
        $user_login = isset($_SESSION['user']['user_login']) ? $_SESSION['user']['user_login'] : '';
        $user = $this->userModel->getUserByUsername($user_login);
        if ($user) {
            $userRole = $user['role'];
            if (!in_array($userRole, $allowedRoles)) {
                return false;
            }
            return true;
        }
    }
    protected function checkRememberToken()
    {
        $token = $this->getRememberToken();

        if ($token) {
            $user = $this->authModel->getUserByRememberToken($token);

            if ($user) {
                $this->setUserSession($user['user_login']);
                return true;
            }
        }

        return false;
    }

    public function isRoleAdmin()
    {
        // Lấy thông tin người dùng từ session hoặc cơ sở dữ liệu
        $userInfo = $this->userModel->getUserById($_SESSION['user']['ID']);

        // Kiểm tra xem người dùng có vai trò là admin hay không
        if ($userInfo && $userInfo['role'] == 'admin') {
            return true;
        }

        return false;
    }
    public function getCurrentUser()
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (isset($_SESSION['user'])) {
            // Lấy thông tin người dùng từ session
            $user = $_SESSION['user'];

            // Trả về thông tin người dùng hiện tại
            return $user;
        }

        // Nếu không có người dùng đăng nhập, trả về null hoặc thông tin mặc định tùy thuộc vào yêu cầu của bạn
        return null;
    }
}
