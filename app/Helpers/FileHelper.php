<?php
namespace App\Helpers;
use Symfony\Component\HttpFoundation\Request;

class FileHelper
{
    private static $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'docx', 'xls', 'xlsx', 'csv', 'xml'];
    private static $maxFileSize = 5242880;
    private static $uploadDirectory = 'uploads';

    public static function uploadFile(Request $request, $fileInputName)
    {
        // Thay thế $_FILES bằng phương thức của Symfony Request
        $fileInfo = $request->files->get($fileInputName);
        echo '<pre>';
        print_r($fileInfo);
        echo '</pre>';
        $fileName = $fileInfo->getClientOriginalName();
        $fileTempName = $fileInfo->getPathname();
        $fileSize = $fileInfo->getSize();
        $fileExtension = strtolower($fileInfo->getClientOriginalExtension());

        // Kiểm tra định dạng file
        if (!in_array($fileExtension, self::$allowedExtensions)) {
            return ['success' => false, 'message' => 'Chỉ cho phép tải lên các định dạng: ' . implode(', ', self::$allowedExtensions)];
        }

        // Kiểm tra kích thước file
        if ($fileSize > self::$maxFileSize) {
            return ['success' => false, 'message' => 'Kích thước file vượt quá giới hạn cho phép'];
        }

        // Tiến hành xử lý tải lên file
        $uploadPath = self::$uploadDirectory . '/' . $fileName;
        if ($fileInfo->move(self::$uploadDirectory, $fileName)) {
            return ['success' => true, 'message' => 'Tải file lên thành công', 'filePath' => $uploadPath];
        } else {
            return ['success' => false, 'message' => 'Có lỗi xảy ra khi tải file lên'];
        }
    }

    // Các phương thức còn lại không cần thay đổi vì không sử dụng $_POST nên không ảnh hưởng bởi việc sử dụng Symfony Request
    public static function setAllowedExtensions(array $extensions)
    {
        self::$allowedExtensions = $extensions;
    }

    public static function setMaxFileSize(int $maxSize)
    {
        self::$maxFileSize = $maxSize;
    }

    public static function setUploadDirectory(string $directory)
    {
        self::$uploadDirectory = $directory;
    }
}
