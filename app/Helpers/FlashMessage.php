<?php

namespace App\Helpers;

trait FlashMessage
{
    public static function success($message)
    {
        self::setFlashMessage('success', $message);
    }

    public static function error($message)
    {
        self::setFlashMessage('error', $message);
    }

    public static function warning($message)
    {
        self::setFlashMessage('warning', $message);
    }

    public static function info($message)
    {
        self::setFlashMessage('info', $message);
    }

    public static function setFlashMessage($type, $message)
    {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function getFlashMessage()
    {
        if (!empty($_SESSION['flash_message'])) {
            $flashMessage = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);

            return $flashMessage;
        }

        return null;
    }

    public static function hasFlashMessage()
    {
        return !empty($_SESSION['flash_message']);
    }

    public static function clearFlashMessage()
    {
        unset($_SESSION['flash_message']);
    }

    public static function displayFlashMessage()
    {
        if (self::hasFlashMessage()) {
            $flashMessage = self::getFlashMessage();
            if ($flashMessage !== null && isset($flashMessage['type']) && isset($flashMessage['message'])) {
                $alertClass = $flashMessage['type'] === 'error' ? 'alert-danger' : 'alert-' . $flashMessage['type'];
                echo '<div class="alert ' . $alertClass . '">';
                echo $flashMessage['message'];
                echo '</div>';
            }
        }
    }
}
