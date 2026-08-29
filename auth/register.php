<?php

include "../includes/header.php";

$error = $_SESSION["auth_error"] ?? "";
unset($_SESSION["auth_error"]);

?>

<h1>Đăng ký tài khoản</h1>

<?php if ($error !== ""): ?>

    <div class="auth-error">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<form action="process_register.php" method="POST">

    <div>
        <label for="name">
            Họ và tên
        </label>

        <input
            type="text"
            id="name"
            name="name"
            maxlength="100"
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
            maxlength="100"
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


    <button type="submit">
        Đăng ký
    </button>

</form>


<p>
    Đã có tài khoản?
    <a href="login.php">
        Đăng nhập
    </a>
</p>


<?php

include "../includes/footer.php";

?>