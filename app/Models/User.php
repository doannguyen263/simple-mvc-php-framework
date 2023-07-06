<?php
namespace App\Models;
use MysqliDb;

class User
{

    protected $db;
    protected $tableName = 'users';
    protected $role;

    public function __construct()
    {
        $this->db = MysqliDb::getInstance();
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
        if ($this->isUsernameTaken($username)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'user_login' => $username,
            'user_pass' => $hashedPassword
        ];
        $this->db->insert($this->tableName, $data);

        return true;
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

    public function getUserInfo($username)
    {
        return $this->db
            ->where('user_login', $username)
            ->getOne($this->tableName, ['id', 'user_login','user_fullname','user_email','role']);
    }

    protected function setUserSession($username)
    {
        
        $_SESSION['user'] = $this->getUserInfo($username);

    }

    protected function unsetUserSession()
    {
        unset($_SESSION['user']);
    }

    protected function isUsernameTaken($username)
    {
        $user = $this->db
            ->where('user_login', $username)
            ->getValue($this->tableName, 'id');

        return ($user !== null);
    }

    public function authenticate($username, $password)
    {
        $user = $this->db
            ->where('user_login', $username)
            ->getOne($this->tableName, ['id', 'user_pass']);

        if ($user && password_verify($password, $user['user_pass'])) {
            return true;
            
        } else {
            return false;
        }
    }

    public function checkAuthentication()
	{
        if (!$this->isLoggedIn() && !$this->checkRememberToken()) {
	        header("Location: login");
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
        $user = $this->getUserInfo($user_login);
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
            ->getOne($this->tableName, ['id', 'user_login']);
    }
    protected function saveRememberTokenToDatabase($username, $token)
    {
        $this->db
            ->where('user_login', $username)
            ->update($this->tableName, ['remember_token' => $token]);
    }
    public function getUserById($userId)
    {
        return $this->db
            ->where('id', $userId)
            ->getOne($this->tableName, ['id', 'user_login','user_fullname','user_email','role']);
    }
    public function getAllUsers()
    {
        return $this->db->get($this->tableName);
    }
    public function updateUser($userId, $dataArray)
    {
        $data = array_intersect_key(
            $dataArray, 
            [
                'user_fullname' => '',
            ]
        );
    
        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }

        $this->db->where('id', $userId);
        $this->db->update($this->tableName, $data);
        return $this->db->count;
    }
    public function verifyPassword($userId, $password)
    {
        $user = $this->db
            ->where('id', $userId)
            ->getOne($this->tableName, ['id', 'user_pass']);

            
        print_r('verifyPassword load'.'<br>');
        print_r('oldPassword load: '.  $user.'<br>');
        print_r('new_password load: '. $user['user_pass'].'<br>');
        die(1);


        if ($user && password_verify($password, $user['user_pass'])) {
            return true;
        } else {
            return false;
        }
    }
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $data = [
            'user_pass' => $hashedPassword
        ];
        
        $this->db
            ->where('id', $userId)
            ->update($this->tableName, $data);
    }
}
