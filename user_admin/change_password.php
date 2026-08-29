<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: change_password.php

MỤC ĐÍCH:
- Hiển thị form đổi mật khẩu

QUYỀN:
- Phải đăng nhập

DỮ LIỆU GỬI:
- current_password
- new_password
- confirm_password

LUỒNG:
change_password.php
        ↓ POST
process_change_password.php
        ↓
kiểm tra mật khẩu cũ
        ↓
password_hash()
        ↓
UPDATE users
==================================================
*/

require_once __DIR__ . "/../config/auth.php";

requireLogin();

include __DIR__ . "/../includes/header.php";

?>

<h1>Đổi mật khẩu</h1>

<form
    action="process_change_password.php"
    method="POST"
>

    <div>
        <label for="current_password">
            Mật khẩu hiện tại
        </label>

        <input
            type="password"
            id="current_password"
            name="current_password"
            required
        >
    </div>


    <div>
        <label for="new_password">
            Mật khẩu mới
        </label>

        <input
            type="password"
            id="new_password"
            name="new_password"
            required
        >
    </div>


    <div>
        <label for="confirm_password">
            Xác nhận mật khẩu mới
        </label>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            required
        >
    </div>


    <button type="submit">
        Đổi mật khẩu
    </button>

</form>


<p>
    <a href="profile.php">
        Quay lại hồ sơ
    </a>
</p>

<?php

include __DIR__ . "/../includes/footer.php";

?>