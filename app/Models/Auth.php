<?php

namespace App\Models;

use MysqliDb;

class Auth
{

  protected $db;
  protected $tableName = 'users';

  public function __construct()
  {
    $this->db = MysqliDb::getInstance();
  }

  public function isUsernameTaken($username)
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

  public function getUserByRememberToken($token)
  {
    return $this->db
      ->where('remember_token', $token)
      ->getOne($this->tableName, ['ID', 'user_login']);
  }
  public function saveRememberTokenToDatabase($username, $token)
  {
    $this->db
      ->where('user_login', $username)
      ->update($this->tableName, ['remember_token' => $token]);
  }
}
