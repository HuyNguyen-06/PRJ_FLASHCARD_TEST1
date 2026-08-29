<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: process_change_password.php

MỤC ĐÍCH:
- Kiểm tra mật khẩu hiện tại
- Kiểm tra mật khẩu mới
- Đổi mật khẩu trong database

USER_ID:
- lấy từ Session
==================================================
*/


// 1. Login.
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

    $currentPassword =
        $_POST["current_password"] ?? "";

    $newPassword =
        $_POST["new_password"] ?? "";

    $confirmPassword =
        $_POST["confirm_password"] ?? "";


    // 5. Không để trống.
    if (
        $currentPassword == ""
        ||
        $newPassword == ""
        ||
        $confirmPassword == ""
    ) {
        die("Vui lòng nhập đầy đủ thông tin.");
    }


    // 6. Mật khẩu mới phải nhập giống nhau.
    if ($newPassword != $confirmPassword) {
        die("Xác nhận mật khẩu mới không khớp.");
    }


    // Có thể đặt yêu cầu tối thiểu.
    if (strlen($newPassword) < 6) {
        die("Mật khẩu mới phải có ít nhất 6 ký tự.");
    }


    // 7. Lấy thông tin user.
    $user = getUserProfile(
        $conn,
        $userId
    );


    if ($user == null) {
        die("Không tìm thấy người dùng.");
    }


    /*
    getUserProfile() hiện không SELECT password.

    Vì vậy ta query riêng password tại đây.
    */
    $sql = "
        SELECT password
        FROM users
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $passwordRow =
        mysqli_fetch_assoc($result);


    // 8. Kiểm tra mật khẩu hiện tại.
    if (
        !$passwordRow
        ||
        !password_verify(
            $currentPassword,
            $passwordRow["password"]
        )
    ) {
        die("Mật khẩu hiện tại không đúng.");
    }


    // 9. Đổi mật khẩu.
    $result = changeUserPassword(
        $conn,
        $userId,
        $newPassword
    );


    if (!$result) {
        die("Đổi mật khẩu thất bại.");
    }


    header(
        "Location: /PRJ_FLASHCARD/user_admin/profile.php"
    );

    exit();
}