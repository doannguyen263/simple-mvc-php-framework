<?php 
namespace App\Controllers;

use App\Models\User;

class BaseController {
  protected $user;
  protected $auth;

  public function __construct() {
    session_start();
    $this->user = new User(); // Khởi tạo đối tượng User
    $this->auth = new AuthController(); // Khởi tạo đối tượng User
    $this->checkAuthentication();
  }

  private function checkAuthentication() {
      if (!$this->auth->isLoggedIn()) {
          // Người dùng chưa đăng nhập, xử lý theo yêu cầu của bạn
          // Ví dụ: chuyển hướng đến trang đăng nhập
          header("Location: /login");
          exit();
      }
  }
}