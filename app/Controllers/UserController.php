<?php
namespace App\Controllers;

use App\Helpers\CSRFTokenTrait;
use App\Helpers\FlashMessage;
use Symfony\Component\HttpFoundation\Request;
use App\Models\User;

class UserController
{
    use CSRFTokenTrait;
    use FlashMessage;
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
            $new_password = $this->request->get('new_password');
            $csrf_user_edit_token = $this->request->get('csrf_user_edit_token');

            $verifyCSRFToken = $this->verifyCSRFToken($csrf_user_edit_token, 'csrf_user_edit_token');
            
            if(!$verifyCSRFToken) {
                $this->setFlashMessage('error','verifyCSRFToken. Cập nhật thất bại.');
            }

            $data = array(
                'user_fullname' => $user_fullname,
            );
            $result = $this->userModel->updateUser($userId, $data);

            if ($result) {
                $this->setFlashMessage('success', 'Cập nhật thông tin người dùng thành công.');
            } else {
                $this->setFlashMessage('error', 'Cập nhật thông tin người dùng thất bại.');
            }

            if (!empty($new_password)) {
                $passwordResult = $this->updatePassword($userId, $new_password);
                if ($passwordResult) {
                    $this->setFlashMessage('success', 'Cập nhật mật khẩu thành công.');
                } else {
                    $this->setFlashMessage('error', 'Cập nhật mật khẩu thất bại.');
                }
            }
            // Thực hiện redirect về trang hiện tại sau khi cập nhật
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function updatePassword($userId, $new_password)
    {
        // Lấy mật khẩu cũ từ form
        $oldPassword = $this->request->get('old_password');

        // Kiểm tra mật khẩu cũ có khớp với mật khẩu hiện tại trong database không
        if (!$this->userModel->verifyPassword($oldPassword, $new_password)) {
            // Xử lý khi mật khẩu cũ không khớp
            return false;
        }
        print_r('verifyPassword true');
        die(1);
        // Hash mật khẩu mới
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu trong database
        $this->userModel->updatePassword($userId, $hashedPassword);

        // Hiển thị thông báo thành công
        $this->setFlashMessage('success', 'Cập nhật mật khẩu thành công.');

        // Chuyển hướng về trang chủ hoặc trang cá nhân
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function profile()
    {

    }
}
?>
