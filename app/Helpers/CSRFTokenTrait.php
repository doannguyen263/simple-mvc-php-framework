<?php
namespace App\Helpers;

trait CSRFTokenTrait
{
    protected function generateCSRFToken($name = 'csrf_token')
    {
        if (!isset($_SESSION[$name])) {
            $_SESSION[$name] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$name];
    }

    protected function verifyCSRFToken($token, $name = 'csrf_token')
    {
        if (!empty($_SESSION[$name]) && hash_equals($_SESSION[$name], $token)) {
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }

    protected function generateCSRFTokenInput($name = 'csrf_token')
    {
        $token = $this->generateCSRFToken($name);
        return '<input type="hidden" name="' . $name . '" value="' . $token . '">';
    }
}