<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN

FILE: settings.php

MỤC ĐÍCH:
- Hiển thị cài đặt cá nhân

CÀI ĐẶT HIỆN CÓ:
- theme
- notif_email

QUYỀN:
- Phải đăng nhập
==================================================
*/


require_once __DIR__ . "/../config/auth.php";

requireLogin();


require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


require_once __DIR__ . "/functions.php";


$userId = $_SESSION["user_id"];

$user = getUserProfile(
    $conn,
    $userId
);


if ($user == null) {
    die("Không tìm thấy người dùng.");
}


include __DIR__ . "/../includes/header.php";

?>

<h1>Cài đặt</h1>


<form
    action="process_settings.php"
    method="POST"
>

    <div>

        <label for="theme">
            Giao diện
        </label>

        <select
            id="theme"
            name="theme"
        >

            <option
                value="light"
                <?php
                if ($user["theme"] == "light") {
                    echo "selected";
                }
                ?>
            >
                Sáng
            </option>


            <option
                value="dark"
                <?php
                if ($user["theme"] == "dark") {
                    echo "selected";
                }
                ?>
            >
                Tối
            </option>

        </select>

    </div>


    <div>

        <label>

            <input
                type="checkbox"
                name="notif_email"
                value="1"

                <?php
                if ($user["notif_email"] == 1) {
                    echo "checked";
                }
                ?>
            >

            Nhận thông báo qua Email

        </label>

    </div>


    <button type="submit">
        Lưu cài đặt
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