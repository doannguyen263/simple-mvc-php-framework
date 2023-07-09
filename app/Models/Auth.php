<?php

namespace App\Models;

use MysqliDb;
use App\Models\User;
use App\Helpers\FlashMessage;

class Auth
{

  protected $db;
  protected $tableName = 'users';
  protected $role;
  protected $modelUser;

  public function __construct()
  {
    $this->db = MysqliDb::getInstance();

    $modelUser = new User;
    $this->modelUser = $modelUser;
  }
  public function login($username, $password, $remember)
  {
    if ($this->authenticate($username, $password)) {

      $this->setUserSession($username);

      // Thiết lập remember token khi đăng nhập thành công và tùy chọn Remember Me được chọn
      if ($remember) {
        $token = $this->generateRememberToken();
        $this->setRememberToken($token);
        $this->saveRememberTokenToDatabase($username, $token);
      }

      return true;
    } else {
      return false;
    }
  }

  public function register($username, $password)
  {
    // Incoming
  }
  protected function setUserSession($username)
  {

    $_SESSION['user'] = $this->modelUser->getUserByUsername($username);
  }

  protected function unsetUserSession()
  {
    unset($_SESSION['user']);
  }

  protected function isUsernameTaken($username)
  {
    $user = $this->db
      ->where('user_login', $username)
      ->getValue($this->tableName, 'ID');

    return ($user !== null);
  }

  public function authenticate($username, $password)
  {
    $user = $this->db
      ->where('user_login', $username)
      ->getOne($this->tableName, ['ID', 'user_pass']);

    if ($user && password_verify($password, $user['user_pass'])) {
      return true;
    } else {
      return false;
    }
  }

  public function logout()
  {
    $this->unsetUserSession();
    $this->clearRememberToken();
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
  public function checkAdmin($urlRedirect = SITE_URL)
  {
    if (!$this->isAdmin()) {
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
    $user = $this->modelUser->getUserByUsername($user_login);
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
      $user = $this->getUserByRememberToken($token);

      if ($user) {
        $this->setUserSession($user['user_login']);
        return true;
      }
    }

    return false;
  }
  protected function getUserByRememberToken($token)
  {
    return $this->db
      ->where('remember_token', $token)
      ->getOne($this->tableName, ['ID', 'user_login']);
  }
  protected function saveRememberTokenToDatabase($username, $token)
  {
    $this->db
      ->where('user_login', $username)
      ->update($this->tableName, ['remember_token' => $token]);
  }
  public function isAdmin()
  {
    // Lấy thông tin người dùng từ session hoặc cơ sở dữ liệu
    $userInfo = $this->modelUser->getUserById($_SESSION['user']['ID']);

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
