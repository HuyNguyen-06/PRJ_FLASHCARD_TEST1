<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: process_update_profile.php

MỤC ĐÍCH:
- Nhận dữ liệu từ profile.php
- Lấy user_id từ Session
- UPDATE hồ sơ trong bảng users

NHẬN:
- name
- email
- interests

KHÔNG NHẬN:
- user_id
- role
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Functions.
require_once __DIR__ . "/functions.php";


// 4. Chỉ xử lý POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = $_SESSION["user_id"];

    $name = trim(
        $_POST["name"] ?? ""
    );

    $email = trim(
        $_POST["email"] ?? ""
    );

    $interests = trim(
        $_POST["interests"] ?? ""
    );


    // 5. Kiểm tra dữ liệu.
    if ($name == "") {
        die("Họ tên không được để trống.");
    }


    if ($email == "") {
        die("Email không được để trống.");
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email không hợp lệ.");
    }


    // 6. UPDATE.
    $result = updateUserProfile(
        $conn,
        $userId,
        $name,
        $email,
        $interests
    );


    if (!$result) {
        die("Cập nhật hồ sơ thất bại.");
    }


    /*
    Name được dùng ở Header/Dashboard,
    nên Session cũng cần cập nhật lại.
    */
    $_SESSION["user_name"] = $name;


    header(
        "Location: /PRJ_FLASHCARD/user_admin/profile.php"
    );

    exit();
}