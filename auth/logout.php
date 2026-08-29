<?php

require_once __DIR__ . "/../config/auth.php";


// Xóa toàn bộ dữ liệu Session
$_SESSION = [];


// Hủy Session
session_destroy();


// Quay về trang đăng nhập
header(
    "Location: /PRJ_FLASHCARD/auth/login.php"
);

exit();