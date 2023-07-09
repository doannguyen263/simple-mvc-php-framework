<?php

namespace App\Helpers;

class View
{
    protected static $beforeBodyContent = '';
    protected static $afterBodyContent = '';
    protected static $beforeFooterContent = '';
    protected static $afterFooterContent = '';

    public static function render($viewName, $data = [])
    {
        $viewPath = self::getViewPath($viewName);
        try {
            require PATH_VIEWS_CORE . '/layouts/html-start.php';
            self::requireView($viewPath, $data);
            require PATH_VIEWS_CORE . '/layouts/html-end.php';
        } catch (\Exception $e) {
            // Xử lý ngoại lệ theo ý muốn, ví dụ: ghi log, hiển thị thông báo lỗi, ...
            throw $e;
        }
    }

    private static function getViewPath($viewName)
    {
        $viewPath = PATH_VIEWS . '/' . $viewName . '.php';
        if (!is_readable($viewPath)) {
            throw new \Exception('File view không tồn tại hoặc không thể đọc.');
        }
        return $viewPath;
    }

    private static function requireView($viewPath, $data)
    {
        extract($data);
        require_once $viewPath;
    }

    public static function renderHeader($headerName = 'header')
    {
        $headerPath = PATH_VIEWS_ADMIN . '/layout/' . $headerName . '.php';
        if (is_file($headerPath)) {
            ob_start();
            require $headerPath;
            echo self::$beforeBodyContent;
            require PATH_VIEWS_CORE . '/layouts/body-start.php';
            echo self::$afterBodyContent;
            echo ob_get_clean();
        } else {
            echo 'File header không tồn tại.';
        }
    }
    public static function addBeforeBody($content)
    {
        self::$beforeBodyContent .= $content;
    }

    public static function addAfterBody($content)
    {
        self::$afterBodyContent.= $content;
    }

    public static function renderFooter($footerName = 'footer')
    {
        $footerPath = PATH_VIEWS_ADMIN . '/layout/' . $footerName . '.php';

        if (is_file($footerPath)) {
            ob_start();
            echo self::$beforeFooterContent;
            require $footerPath;
            echo self::$afterFooterContent;
            echo ob_get_clean();
        } else {
            echo 'File footer không tồn tại.';
        }
    }

    public static function addBeforeFooter($content)
    {
        self::$beforeFooterContent .= $content;
    }

    public static function addAfterFooter($content)
    {
        self::$afterFooterContent .= $content;
    }
}
