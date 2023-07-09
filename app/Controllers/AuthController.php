<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Helpers\CSRFTokenTrait;
use App\Models\Auth;

class AuthController
{
    use FlashMessage;
    use CSRFTokenTrait;

    protected $userModel;
    protected $request;
    protected $auth;

    public function __construct()
    {
      $modelAuth = new Auth;
      $this->auth = $modelAuth;
      $this->request = Request::createFromGlobals();
    }

    public function login()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->request->get('username');
            $password = $this->request->get('password');
            $remember = $this->request->get('remember');
            $csrf_login_token = $this->request->get('csrf_login_token');

            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_login_token, 'csrf_login_token');
    
            if(!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error','verifyCSRFToken. Cập nhật thất bại.');
            }

		    $auth = $this->auth->login($username, $password, $remember);
            if (!$auth) {
                FlashMessage::error('Tài khoản hoặc mật khẩu sai!');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        $request = Request::createFromGlobals();
        if ($_SERVER['REQUEST_METHOD'] === 'GET' &&  $request->query->get('action') == 'logout') { 
            $this->auth->logout();
        }

        if($this->auth->isLoggedIn()) {
            header("Location: ".SITE_URL);
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
        $this->auth->logout();
    }
}