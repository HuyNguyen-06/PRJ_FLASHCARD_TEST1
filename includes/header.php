<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard IT</title>

    <link rel="stylesheet" href="/PRJ_FLASHCARD/assets/css/style.css">
</head>

<body>

<header>
    <h2>Flashcard IT</h2>

 <nav>

    <!-- PUBLIC: ai cũng thấy -->
    <a href="/PRJ_FLASHCARD/index.php">
        Trang chủ
    </a>

    <a href="/PRJ_FLASHCARD/sets/index.php">
        Bộ thẻ
    </a>


    <?php if (isset($_SESSION["user_id"])): ?>

        <!-- Chỉ người đã login thấy -->
        <a href="/PRJ_FLASHCARD/dashboard.php">
            Dashboard
        </a>

        <a href="/PRJ_FLASHCARD/progress/history.php">
            Tiến độ
        </a>

        <a href="/PRJ_FLASHCARD/user_admin/profile.php">
            Hồ sơ
        </a>


        <?php if (
            isset($_SESSION["role"])
            &&
            $_SESSION["role"] == "admin"
        ): ?>

            <a href="/PRJ_FLASHCARD/user_admin/admin/dashboard.php">
                Admin
            </a>

        <?php endif; ?>


        <a href="/PRJ_FLASHCARD/auth/logout.php">
            Đăng xuất
        </a>


    <?php else: ?>

        <a href="/PRJ_FLASHCARD/auth/login.php">
            Đăng nhập
        </a>

        <a href="/PRJ_FLASHCARD/auth/register.php">
            Đăng ký
        </a>

    <?php endif; ?>

</nav>
</header>

<main>