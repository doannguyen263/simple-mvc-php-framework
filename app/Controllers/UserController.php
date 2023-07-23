<?php

namespace App\Controllers;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use App\Helpers\View;
use App\Middleware\CSRFTokenTrait;
use App\Helpers\FlashMessage;
use App\Controllers\AuthController;
use App\Models\User;

class UserController
{
    use CSRFTokenTrait;
    use FlashMessage;

    protected $authModel;
    protected $userModel;
    protected $request;

    public function __construct()
    {
        $userModel = new User;
        $this->userModel = $userModel;

        $authModel = new AuthController;
        $this->authModel = $authModel;
        $authModel->checkUserLogin();

        $this->request = Request::createFromGlobals();
    }

    public function index(RouteCollection $routes)
    {

        $page = $_GET['page'] ?? 1;
        $perPage = 5;

        $data = $this->userModel->getUsersWithPagination($page, $perPage);

        // Truyền dữ liệu phân trang cho view
        $data = [
            'users' => $data['data'],
            'totalPages' => $data['totalPages']
        ];
        View::render('admin/user/index', $data);
    }

    public function create()
    {
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        $this->authModel->checkRoleAdmin();

        $this->store();
        View::render('admin/user/add');
    }

    function store()
    {
        // Kiểm tra xem yêu cầu là phương thức POST hay không
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ biểu mẫu
            $user_login = $this->request->get('user_login');
            $user_fullname = $this->request->get('user_fullname');
            $user_email = $this->request->get('user_email');
            $user_pass = $this->request->get('user_pass');
            $role = $this->request->get('role');

            $csrf_user_create_token = $this->request->get('csrf_user_create_token');
            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_user_create_token, 'csrf_user_create_token');

            if (!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error', 'verifyCSRFToken. Cập nhật thất bại.');
            }

            $password_hash = password_hash($user_pass, PASSWORD_DEFAULT);
            $data = array(
                'user_login' => $user_login,
                'user_fullname' => $user_fullname,
                'user_email' => $user_email,
                'user_pass' => $password_hash,
                'role' => $role,
            );
            if (empty($data['user_fullname']) || empty($data['user_email']) || empty($data['user_login']) || empty($data['user_pass'])) {
                FlashMessage::error('Vui lòng nhập hết trường bắt buộc.');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Kiểm tra trùng user_email
            if ($this->userModel->isUserEmailExists($data['user_email'])) {
                FlashMessage::error('Email đã tồn tại.');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Kiểm tra trùng user_login
            if ($this->userModel->isUserLoginExists($data['user_login'])) {
                FlashMessage::error('Username đã tồn tại.');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Thực hiện kiểm tra và thêm người dùng vào cơ sở dữ liệu
            $result = $this->userModel->addUser($data);
            if ($result) {
                // Nếu thêm thành công, đặt thông báo flash và chuyển hướng đến trang danh sách người dùng
                FlashMessage::success('Thêm người dùng thành công.');
                // Thực hiện redirect về trang hiện tại sau khi cập nhật
                header("Location: user-index");
                exit;
                
            } else {
                // Nếu thêm thất bại, đặt thông báo flash và hiển thị lại biểu mẫu
                FlashMessage::error('Thêm người dùng thất bại.');
            }
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function edit()
    {
        $userId = $this->request->get('user_id');

        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        if ($userId && $userId != $_SESSION['user']['ID']) {
            // Xử lý khi người dùng không có quyền xóa
            $this->authModel->checkRoleAdmin();
        }


        // Kiểm tra xem có truyền vào $userId không
        if ($userId == null) {
            // Nếu không truyền vào, lấy userId từ session
            $userId = $_SESSION['user']['ID'];
        }


        // Call update func
        $this->update();

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            die('ID người dùng không hợp lệ.');
        }

        // Hiển thị form chỉnh sửa thông tin người dùng
        View::render('admin/user/edit', ['user' => $user]);
    }

    public function update()
    {
        if ($this->request->isMethod('POST')) {
            $userId = $this->request->get('user_id');
            $user_fullname = $this->request->get('user_fullname');
            $new_password = $this->request->get('new_password');
            $csrf_user_edit_token = $this->request->get('csrf_user_edit_token');
            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_user_edit_token, 'csrf_user_edit_token');

            if (!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error', 'verifyCSRFToken. Cập nhật thất bại.');
            }

            $data = array(
                'user_fullname' => $user_fullname,
            );
            $result = $this->userModel->updateUser($userId, $data);

            if ($result) {
                FlashMessage::setFlashMessage('success', 'Cập nhật thông tin người dùng thành công.');
            } else {
                FlashMessage::setFlashMessage('error', 'Cập nhật thông tin người dùng thất bại.');
            }

            if (!empty($new_password)) {
                $this->updatePassword($userId, $new_password);
            }
            // Thực hiện redirect về trang hiện tại sau khi cập nhật
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function updatePassword()
    {
        // Lấy mật khẩu cũ từ form
        $userId = $this->request->get('user_id');
        $currentPassword = $this->request->get('current_password');
        $newPassword = $this->request->get('new_password');
        $confirmPassword = $this->request->get('confirm_password');

        // Kiểm tra mật khẩu hiện tại của người dùng
        if (!$this->userModel->verifyPassword($userId, $currentPassword)) {
            FlashMessage::setFlashMessage('error', 'Mật khẩu hiện tại không đúng.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
        // Kiểm tra mật khẩu mới và xác nhận mật khẩu có khớp nhau
        if ($newPassword !== $confirmPassword) {
            FlashMessage::setFlashMessage('error', 'Mật khẩu mới và xác nhận mật khẩu không khớp.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
        // Cập nhật mật khẩu mới vào cơ sở dữ liệu
        $result = $this->userModel->updatePassword($userId, $newPassword);

        if ($result) {
            FlashMessage::setFlashMessage('success', 'Cập nhật mật khẩu thành công.');
        } else {
            FlashMessage::setFlashMessage('error', 'Cập nhật mật khẩu thất bại.');
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function destroy()
    {
        $userId = $this->request->get('user_id');

        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        if (!$this->authModel->isRoleAdmin()) {
            // Xử lý khi người dùng không có quyền xóa
            FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Xóa người dùng từ cơ sở dữ liệu
        $result = $this->userModel->deleteUser($userId);

        if ($result) {
            FlashMessage::setFlashMessage('success', 'Xóa người dùng thành công.');
        } else {
            FlashMessage::setFlashMessage('error', 'Xóa người dùng thất bại.');
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
