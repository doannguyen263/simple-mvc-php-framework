<?php
namespace App\Helpers;

class ResponseHelper
{
    public static function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    public static function successResponse($message = 'Success', $data = null, $statusCode = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        self::jsonResponse($response, $statusCode);
    }

    public static function errorResponse($message = 'Error', $statusCode = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        self::jsonResponse($response, $statusCode);
    }

    public static function redirectResponse($url)
    {
        header("Location: $url");
        exit;
    }
}