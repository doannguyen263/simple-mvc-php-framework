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

    public function getUserCurrent()
    {
        $userId = $_SESSION['user']['ID'];
        return $this->db
            ->where('ID', $userId)
            ->getOne($this->tableName, ['ID', 'user_login', 'user_fullname', 'user_email', 'role']);
    }
    public function getUserCurrentID()
    {
        return $_SESSION['user']['ID'];
    }

    public function getUserByUsername($username)
    {
        return $this->db
            ->where('user_login', $username)
            ->getOne($this->tableName, ['ID', 'user_login', 'user_fullname', 'user_email', 'role']);
    }

    public function getUserById($userId)
    {
        return $this->db
            ->where('ID', $userId)
            ->getOne($this->tableName, ['ID', 'user_login', 'user_fullname', 'user_email', 'role']);
    }
    public function getAllUsers()
    {
        return $this->db->get($this->tableName);
    }
    public function getUsersWithPagination($currentPage, $perPage = 10)
    {
        $this->db->pageLimit = $perPage;
        $users = $this->db->arraybuilder()->paginate($this->tableName, $currentPage);

        $totalPages = $this->db->totalPages;
        $totalRecords = $this->db->totalCount;
        return [
            'data' => $users,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
        ];
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

        $this->db->where('ID', $userId);
        $this->db->update($this->tableName, $data);
        return $this->db->count;
    }
    public function addUser($dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'user_login' => '',
                'user_fullname' => '',
                'user_email' => '',
                'user_pass' => '',
                'role' => '',
            ]
        );

        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['created_at'] = $this->db->now();
        $result = $this->db->insert($this->tableName, $data);
        return $result;
    }

    public function verifyPassword($userId, $password)
    {
        $user = $this->db
            ->where('ID', $userId)
            ->getOne($this->tableName, ['ID', 'user_pass']);

        if (!$user) {
            return false;
        }
        return password_verify($password, $user['user_pass']);;
    }
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $data = [
            'user_pass' => $hashedPassword
        ];

        return $this->db
            ->where('ID', $userId)
            ->update($this->tableName, $data);
    }
    public function deleteUser($userId)
    {
        // Xóa người dùng từ cơ sở dữ liệu
        $result = $this->db->where('ID', $userId)->delete($this->tableName);
        return $result;
    }
    public function isUserEmailExists($email)
    {
        // Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $existingUserEmail = $this->db->where('user_email', $email)->getOne($this->tableName);
        return $existingUserEmail ? true : false;
    }

    public function isUserLoginExists($login)
    {
        $existingUserLogin = $this->db->where('user_login', $login)->getOne($this->tableName);
        return $existingUserLogin ? true : false;
    }
}
