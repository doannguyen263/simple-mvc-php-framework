<?php
namespace App\Controllers;

use App\Helpers\CSRFTokenTrait;
use Symfony\Component\HttpFoundation\Request;
use App\Models\User;

class UserController
{
    use CSRFTokenTrait;
    protected $userModel;
    protected $request;

    public function __construct()
    {
        session_start();
        $modelUser = new User;
        $modelUser->checkAuthentication();

        $this->userModel = $modelUser;
        $this->request = Request::createFromGlobals();
    }

    public function index()
    {
        // Lấy danh sách người dùng từ model
        $userModel = new User();
        $users = $userModel->getAllUsers();

        // Gọi view để hiển thị danh sách người dùng
        require_once URL_VIEWS_ADMIN . '/user/index.php';
    }
    public function create()
    {
        // Lấy danh sách người dùng từ model
        $users = $this->userModel->getAllUsers();

        // Gọi view để hiển thị danh sách người dùng
        require_once URL_VIEWS_ADMIN . '/user/index.php';
    }
    public function store()
    {
        $users = $this->userModel->getAllUsers();
        // Gọi view để hiển thị danh sách người dùng
        require_once URL_VIEWS_ADMIN . '/user/index.php';
    }
    public function edit()
    {
        $userId = $this->request->get('user_id');

        // Kiểm tra xem có truyền vào $userId không
        if ($userId == null) {
            // Nếu không truyền vào, lấy userId từ session
            $userId = $_SESSION['user']['id'];
        }
    
        $response = $this->update();

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            die('ID người dùng không hợp lệ.');
        }

        // Hiển thị form chỉnh sửa thông tin người dùng
        require_once URL_VIEWS_ADMIN . '/user/edit.php';
    }

    public function update(){
        if ($this->request->isMethod('POST')) {

            $userId = $this->request->get('user_id');
            $user_fullname = $this->request->get('user_fullname');
            $user_pass = $this->request->get('user_pass');
            $csrf_user_edit_token = $this->request->get('csrf_user_edit_token');

            $verifyCSRFToken = $this->verifyCSRFToken($csrf_user_edit_token, 'csrf_user_edit_token');
            
            if(!$verifyCSRFToken) {
                return [
                    'status' => 'error',
                    'message' => 'verifyCSRFToken. Cập nhật thất bại.'
                ];
            }

            $data = array(
                'user_fullname' => $user_fullname,
            );
            $result = $this->userModel->updateUser($userId, $data);

            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Cập nhật thành công.'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Cập nhật thất bại.'
                ];
            }
        }
    }

    public function updatePassword()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!$this->userModel->isLoggedIn()) {
            // Xử lý khi chưa đăng nhập
            return false;
        }
      
        // Lấy thông tin người dùng từ session
        $user = $_SESSION['user'];

        // Lấy mật khẩu cũ từ form
        $oldPassword = $_POST['old_password'];

        // Kiểm tra mật khẩu cũ có khớp với mật khẩu hiện tại trong database không
        if (!$this->userModel->verifyPassword($oldPassword, $user['password'])) {
            // Xử lý khi mật khẩu cũ không khớp
            return false;
        }

        // Lấy mật khẩu mới từ form
        $newPassword = $_POST['new_password'];

        // Hash mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu trong database
        $this->userModel->updatePassword($user['id'], $hashedPassword);

        // Hiển thị thông báo thành công
        $this->setFlashMessage('success', 'Cập nhật mật khẩu thành công.');

        // Chuyển hướng về trang chủ hoặc trang cá nhân
        header('Location: /');
        exit;
    }

    public function profile()
    {

    }
}
?>
