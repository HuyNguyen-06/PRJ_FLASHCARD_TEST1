<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: profile.php

MỤC ĐÍCH:
- Hiển thị hồ sơ của người đang đăng nhập
- Cho phép chỉnh sửa Name / Email / Interests

QUYỀN:
- Phải đăng nhập

USER_ID:
- lấy từ Session
==================================================
*/


// 1. Kiểm tra login.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Functions User/Admin.
require_once __DIR__ . "/functions.php";


// 4. Lấy profile của user hiện tại.
$userId = $_SESSION["user_id"];

$user = getUserProfile(
    $conn,
    $userId
);


if ($user == null) {
    die("Không tìm thấy người dùng.");
}


// 5. Giao diện.
include __DIR__ . "/../includes/header.php";

?>

<h1>Hồ sơ cá nhân</h1>


<form
    action="process_update_profile.php"
    method="POST"
>

    <div>

        <label for="name">
            Họ và tên
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($user["name"]); ?>"
            required
        >

    </div>


    <div>

        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($user["email"]); ?>"
            required
        >

    </div>


    <div>

        <label for="interests">
            Sở thích / Chủ đề quan tâm
        </label>

        <input
            type="text"
            id="interests"
            name="interests"
            value="<?php echo htmlspecialchars($user["interests"] ?? ""); ?>"
        >

    </div>


    <p>
        Vai trò:
        <strong>
            <?php echo htmlspecialchars($user["role"]); ?>
        </strong>
    </p>


    <p>
        Ngày tạo tài khoản:
        <?php echo htmlspecialchars($user["created_at"]); ?>
    </p>


    <button type="submit">
        Cập nhật hồ sơ
    </button>

</form>


<p>
    <a href="change_password.php">
        Đổi mật khẩu
    </a>
</p>


<p>
    <a href="settings.php">
        Cài đặt
    </a>
</p>


<?php

include __DIR__ . "/../includes/footer.php";

?>