<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/functions.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}


// ================================
// 1. Lấy dữ liệu
// ================================

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";


// ================================
// 2. Validate
// ================================

if ($email === "" || $password === "") {

    $_SESSION["auth_error"] =
        "Vui lòng nhập đầy đủ email và mật khẩu.";

    header("Location: login.php");
    exit();
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["auth_error"] =
        "Email không đúng định dạng.";

    header("Location: login.php");
    exit();
}


// ================================
// 3. Login
// ================================

$user = loginUser(
    $conn,
    $email,
    $password
);


// ================================
// 4. Login thất bại
// ================================

if ($user === null) {

    $_SESSION["auth_error"] =
        "Email hoặc mật khẩu không chính xác.";

    header("Location: login.php");
    exit();
}


// ================================
// 5. Login thành công
// ================================

// Chống session fixation.
session_regenerate_id(true);


// GIỮ NGUYÊN SESSION CONTRACT
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["role"] = $user["role"];


// ================================
// 6. Redirect theo role
// ================================

if ($_SESSION["role"] === "admin") {

    header(
        "Location: /PRJ_FLASHCARD/user_admin/admin/dashboard.php"
    );

} else {

    header(
        "Location: /PRJ_FLASHCARD/dashboard.php"
    );
}

exit();