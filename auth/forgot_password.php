<?php

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/functions.php";


$error = "";
$success = "";
$resetLink = "";


// ==================================================
// TRƯỜNG HỢP 1:
// Người dùng gửi email yêu cầu reset
// ==================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    &&
    isset($_POST["request_reset"])
) {

    $email = trim($_POST["email"] ?? "");


    if ($email === "") {

        $error =
            "Vui lòng nhập email.";

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Email không đúng định dạng.";

    } else {

        $user = getUserByEmail(
            $conn,
            $email
        );


        if ($user === null) {

            // Không tiết lộ email có tồn tại hay không.
            $success =
                "Nếu email tồn tại trong hệ thống, "
                . "liên kết đặt lại mật khẩu đã được tạo.";

        } else {

            $token = createPasswordResetToken(
                $conn,
                $user["id"]
            );


            if ($token === false) {

                $error =
                    "Không thể tạo liên kết đặt lại mật khẩu.";

            } else {

                /*
                 * DEMO LOCALHOST
                 *
                 * Vì project chạy XAMPP và chưa cấu hình SMTP,
                 * link reset được hiển thị trực tiếp.
                 */
                $resetLink =
                    "http://localhost/PRJ_FLASHCARD/auth/forgot_password.php?token="
                    . urlencode($token);

                $success =
                    "Liên kết đặt lại mật khẩu đã được tạo. "
                    . "Liên kết có hiệu lực trong 15 phút.";
            }
        }
    }
}


// ==================================================
// TRƯỜNG HỢP 2:
// Người dùng submit password mới
// ==================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    &&
    isset($_POST["reset_password"])
) {

    $token = trim($_POST["token"] ?? "");

    $password = $_POST["password"] ?? "";

    $confirmPassword =
        $_POST["confirm_password"] ?? "";


    if ($token === "") {

        $error =
            "Token không hợp lệ.";

    } elseif (
        $password === ""
        ||
        $confirmPassword === ""
    ) {

        $error =
            "Vui lòng nhập đầy đủ mật khẩu.";

    } elseif (
        strlen($password) < 8
    ) {

        $error =
            "Mật khẩu phải có ít nhất 8 ký tự.";

    } elseif (
        $password !== $confirmPassword
    ) {

        $error =
            "Mật khẩu xác nhận không khớp.";

    } else {

        $user = getUserByResetToken(
            $conn,
            $token
        );


        if ($user === null) {

            $error =
                "Liên kết không hợp lệ hoặc đã hết hạn.";

        } else {

            $updated = updatePassword(
                $conn,
                $user["id"],
                $password
            );


            if (!$updated) {

                $error =
                    "Không thể cập nhật mật khẩu.";

            } else {

                deletePasswordResetToken(
                    $conn,
                    $user["reset_id"]
                );


                $_SESSION["auth_success"] =
                    "Đổi mật khẩu thành công. "
                    . "Vui lòng đăng nhập lại.";

                header("Location: login.php");
                exit();
            }
        }
    }
}


// ==================================================
// KIỂM TRA TOKEN TRÊN URL
// ==================================================

$tokenFromUrl =
    trim($_GET["token"] ?? "");

$validTokenUser = null;


if ($tokenFromUrl !== "") {

    $validTokenUser =
        getUserByResetToken(
            $conn,
            $tokenFromUrl
        );


    if ($validTokenUser === null) {

        $error =
            "Liên kết không hợp lệ hoặc đã hết hạn.";
    }
}


include "../includes/header.php";

?>


<h1>Quên mật khẩu</h1>


<?php if ($error !== ""): ?>

    <div class="auth-error">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<?php if ($success !== ""): ?>

    <div class="auth-success">
        <?= htmlspecialchars($success) ?>
    </div>

<?php endif; ?>


<?php if ($resetLink !== ""): ?>

    <div class="auth-reset-link">

        <p>
            <strong>
                Link đặt lại mật khẩu (Demo localhost):
            </strong>
        </p>

        <p>
            <a href="<?= htmlspecialchars($resetLink) ?>">
                <?= htmlspecialchars($resetLink) ?>
            </a>
        </p>

        <p>
            Link có hiệu lực trong 15 phút.
        </p>

    </div>

<?php endif; ?>


<?php if ($validTokenUser !== null): ?>


    <!-- ==========================================
         FORM ĐẶT MẬT KHẨU MỚI
         ========================================== -->

    <h2>Đặt mật khẩu mới</h2>

    <p>
        Tài khoản:
        <strong>
            <?= htmlspecialchars($validTokenUser["email"]) ?>
        </strong>
    </p>


    <form method="POST">

        <input
            type="hidden"
            name="token"
            value="<?= htmlspecialchars($tokenFromUrl) ?>"
        >


        <div>

            <label for="password">
                Mật khẩu mới
            </label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
            >

        </div>


        <div>

            <label for="confirm_password">
                Xác nhận mật khẩu
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="8"
                required
            >

        </div>


        <button
            type="submit"
            name="reset_password"
        >
            Đổi mật khẩu
        </button>

    </form>


<?php else: ?>


    <!-- ==========================================
         FORM NHẬP EMAIL
         ========================================== -->

    <form method="POST">

        <div>

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                required
            >

        </div>


        <button
            type="submit"
            name="request_reset"
        >
            Tạo liên kết đặt lại mật khẩu
        </button>

    </form>


<?php endif; ?>


<p>
    <a href="login.php">
        Quay lại đăng nhập
    </a>
</p>


<?php

include "../includes/footer.php";

?>