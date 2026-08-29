<?php

include "../includes/header.php";

$error = $_SESSION["auth_error"] ?? "";
unset($_SESSION["auth_error"]);

$success = $_SESSION["auth_success"] ?? "";
unset($_SESSION["auth_success"]);

?>

<h1>Đăng nhập</h1>


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


<form action="process_login.php" method="POST">

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


    <div>
        <label for="password">
            Mật khẩu
        </label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >
    </div>


    <button type="submit">
        Đăng nhập
    </button>

</form>


<p>
    <a href="forgot_password.php">
        Quên mật khẩu?
    </a>
</p>


<p>
    Chưa có tài khoản?
    <a href="register.php">
        Đăng ký
    </a>
</p>


<?php

include "../includes/footer.php";

?>