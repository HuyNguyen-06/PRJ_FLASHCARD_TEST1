<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN
OWNER: Trần Đăng Khoa

FILE: admin/dashboard.php

MỤC ĐÍCH:
- Hiển thị Dashboard dành riêng cho Admin
- Chỉ tài khoản có role = admin mới được truy cập

SỬ DỤNG:
- config/auth.php
- includes/header.php
- includes/footer.php

SESSION SỬ DỤNG:
- $_SESSION['user_id']
- $_SESSION['user_name']
- $_SESSION['role']

QUYỀN TRUY CẬP:
- Guest  : KHÔNG được vào
- User   : KHÔNG được vào
- Admin  : ĐƯỢC vào

LUỒNG:
Truy cập Admin Dashboard
        ↓
requireAdmin()
        ↓
Đã login chưa?
   ↓
Có role = admin?
   ↓
Có -> cho vào
Không -> dashboard.php
==================================================
*/


require_once __DIR__ . "/../../config/auth.php";
requireAdmin();

require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */

require_once __DIR__ . "/../functions.php";


$statistics = getAdminStatistics($conn);


include __DIR__ . "/../../includes/header.php";

?>

<h1>Admin Dashboard</h1>

<p>
    Xin chào Admin,
    <strong>
        <?php
        echo htmlspecialchars(
            $_SESSION["user_name"]
        );
        ?>
    </strong>
</p>


<div>

    <h2>
        <?php echo $statistics["total_users"]; ?>
    </h2>

    <p>Tổng người dùng</p>

</div>


<div>

    <h2>
        <?php echo $statistics["total_sets"]; ?>
    </h2>

    <p>Tổng bộ Flashcard</p>

</div>


<div>

    <h2>
        <?php echo $statistics["pending_sets"]; ?>
    </h2>

    <p>Bộ đang chờ duyệt</p>

</div>


<div>

    <h2>
        <?php echo $statistics["study_sessions"]; ?>
    </h2>

    <p>Tổng phiên học</p>

</div>


<p>
    <a href="users.php">
        Quản lý người dùng
    </a>
</p>

<p>
    <a href="pending_sets.php">
        Duyệt bộ Flashcard
    </a>
</p>

<?php

include __DIR__ . "/../../includes/footer.php";

?>