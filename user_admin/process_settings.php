<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: process_settings.php

MỤC ĐÍCH:
- Nhận Settings từ form
- UPDATE users
==================================================
*/


require_once __DIR__ . "/../config/auth.php";

requireLogin();


require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


require_once __DIR__ . "/functions.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = $_SESSION["user_id"];

    $theme =
        $_POST["theme"] ?? "light";


    /*
    Checkbox:
    - được tick    -> có notif_email
    - không tick   -> không gửi field này
    */
    $notifEmail =
        isset($_POST["notif_email"])
        ? 1
        : 0;


    // Chỉ chấp nhận theme hợp lệ.
    if (
        $theme != "light"
        &&
        $theme != "dark"
    ) {
        die("Theme không hợp lệ.");
    }


    $result = updateUserSettings(
        $conn,
        $userId,
        $theme,
        $notifEmail
    );


    if (!$result) {
        die("Cập nhật cài đặt thất bại.");
    }


    header(
        "Location: /PRJ_FLASHCARD/user_admin/settings.php"
    );

    exit();
}