<?php

/*
==================================================
FILE: dashboard.php

MỤC ĐÍCH:
- Dashboard chính của người dùng sau khi đăng nhập
- Chỉ người đã đăng nhập mới được truy cập

SỬ DỤNG:
- config/auth.php
- includes/header.php
- includes/footer.php

SESSION SỬ DỤNG:
- $_SESSION['user_id']
- $_SESSION['user_name']
- $_SESSION['role']

LUỒNG:
login thành công
    ↓
tạo Session
    ↓
dashboard.php
    ↓
requireLogin()
    ↓
cho phép truy cập
==================================================
*/


// Khởi động Session và lấy các hàm bảo vệ chung.
require_once __DIR__ . "/config/auth.php";


// Chặn người chưa đăng nhập.
requireLogin();


// Sau khi kiểm tra quyền xong mới hiển thị HTML.
include __DIR__ . "/includes/header.php";

?>

<h1>Dashboard</h1>

<p>
    Xin chào,
    <strong>
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
    </strong>
</p>

<p>
    Bạn đã đăng nhập thành công vào Flashcard IT.
</p>

<?php

include __DIR__ . "/includes/footer.php";

?>