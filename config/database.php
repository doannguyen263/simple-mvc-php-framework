<?php
// Tạo kết nối CSDL
$db = new MysqliDb(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kiểm tra kết nối
if (!$db->ping()) {
    die('Không thể kết nối đến cơ sở dữ liệu: ');
}