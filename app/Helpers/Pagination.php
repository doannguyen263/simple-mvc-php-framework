<?php

namespace App\Helpers;

class Pagination
{
    public static function getCurrentPage()
    {
        return isset($_GET['page']) ? (int) $_GET['page'] : 1;
    }

    public static function getCurrentUrl()
    {
        $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $url .= "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $url;
    }

    private static function generatePageUrl($urlPattern, $page)
    {
        $urlParts = parse_url($urlPattern);
        $query = $urlParts['query'] ?? '';

        parse_str($query, $params);

        $params['page'] = $page;

        $newQuery = http_build_query($params);

        $newUrl = $urlParts['path'] . '?' . $newQuery;

        return $newUrl;
    }

    public static function render($totalPages)
    {
        $currentPage = self::getCurrentPage();
        $urlPattern = self::getCurrentUrl();

        echo '<ul class="pagination">';

        // Render nút Previous
        if ($currentPage > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . self::generatePageUrl($urlPattern, $currentPage - 1) . '">Previous</a></li>';
        }

        // Render các nút trang
        $numLinks = 5; // Số lượng liên kết trang để hiển thị
        $halfNumLinks = floor($numLinks / 2); // Số lượng liên kết trang ở mỗi phía trang hiện tại

        $startPage = max(1, $currentPage - $halfNumLinks);
        $endPage = min($startPage + $numLinks - 1, $totalPages);

        if ($startPage > 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $currentPage) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="' . self::generatePageUrl($urlPattern, $i) . '">' . $i . '</a></li>';
            }
        }

        if ($endPage < $totalPages) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Render nút Next
        if ($currentPage < $totalPages) {
            echo '<li class="page-item"><a class="page-link" href="' . self::generatePageUrl($urlPattern, $currentPage + 1) . '">Next</a></li>';
        }

        echo '</ul>';
    }
}
