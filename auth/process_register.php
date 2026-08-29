<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/functions.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}


// ================================
// 1. Lấy dữ liệu
// ================================

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";


// ================================
// 2. Validate rỗng
// ================================

if (
    $name === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    $_SESSION["auth_error"] =
        "Vui lòng nhập đầy đủ thông tin.";

    header("Location: register.php");
    exit();
}


// ================================
// 3. Validate họ tên
// ================================

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {

    $_SESSION["auth_error"] =
        "Họ và tên phải từ 2 đến 100 ký tự.";

    header("Location: register.php");
    exit();
}


// ================================
// 4. Validate email
// ================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["auth_error"] =
        "Email không đúng định dạng.";

    header("Location: register.php");
    exit();
}


// ================================
// 5. Validate password
// ================================

if (strlen($password) < 8) {

    $_SESSION["auth_error"] =
        "Mật khẩu phải có ít nhất 8 ký tự.";

    header("Location: register.php");
    exit();
}


// ================================
// 6. Kiểm tra confirm password
// ================================

if ($password !== $confirmPassword) {

    $_SESSION["auth_error"] =
        "Mật khẩu xác nhận không khớp.";

    header("Location: register.php");
    exit();
}


// ================================
// 7. Kiểm tra email trùng
// ================================

$existingUser = getUserByEmail(
    $conn,
    $email
);

if ($existingUser !== null) {

    $_SESSION["auth_error"] =
        "Email này đã được đăng ký.";

    header("Location: register.php");
    exit();
}


// ================================
// 8. Tạo tài khoản
// ================================

$result = registerUser(
    $conn,
    $name,
    $email,
    $password
);


if (!$result) {

    $_SESSION["auth_error"] =
        "Đăng ký thất bại. Vui lòng thử lại.";

    header("Location: register.php");
    exit();
}


// ================================
// 9. Thành công
// ================================

$_SESSION["auth_success"] =
    "Đăng ký thành công. Vui lòng đăng nhập.";

header("Location: login.php");
exit();