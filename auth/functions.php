<?php

/*
==================================================
MODULE: XÁC THỰC NGƯỜI DÙNG
OWNER: Nguyễn Ngọc Huy

FILE: functions.php

MỤC ĐÍCH:
- Tìm user theo email
- Đăng ký tài khoản
- Đăng nhập
- Tạo reset password token
- Kiểm tra reset token
- Xóa reset token

KHÔNG:
- Không xử lý giao diện
- Không quản lý Flashcard
- Không quản lý Profile
- Không khai báo Session helper
==================================================
*/


/*
--------------------------------------------------
HÀM: getUserByEmail()
--------------------------------------------------
*/
function getUserByEmail($conn, $email)
{
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $user ?: null;
}


/*
--------------------------------------------------
HÀM: registerUser()
--------------------------------------------------
*/
function registerUser($conn, $name, $email, $password)
{
    $passwordHash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql = "
        INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, 'user')
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $name,
        $email,
        $passwordHash
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}


/*
--------------------------------------------------
HÀM: loginUser()
--------------------------------------------------
*/
function loginUser($conn, $email, $password)
{
    $user = getUserByEmail($conn, $email);

    if ($user === null) {
        return null;
    }

    if (!password_verify($password, $user["password"])) {
        return null;
    }

    return $user;
}


/*
--------------------------------------------------
HÀM: createPasswordResetToken()
--------------------------------------------------

Tạo token ngẫu nhiên cho chức năng
Forgot Password.

Token thật không lưu trực tiếp vào database.
Chỉ lưu SHA-256 hash.
--------------------------------------------------
*/
function createPasswordResetToken($conn, $userId)
{
    // Xóa các token cũ của user
    $deleteSql = "
        DELETE FROM password_resets
        WHERE user_id = ?
    ";

    $deleteStmt = mysqli_prepare($conn, $deleteSql);

    if ($deleteStmt) {
        mysqli_stmt_bind_param(
            $deleteStmt,
            "i",
            $userId
        );

        mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
    }


    // Tạo token ngẫu nhiên
    $token = bin2hex(random_bytes(32));

    // Chỉ lưu hash vào database
    $tokenHash = hash(
        "sha256",
        $token
    );

    // Token hết hạn sau 15 phút
    $expiresAt = date(
        "Y-m-d H:i:s",
        time() + 15 * 60
    );


    $sql = "
        INSERT INTO password_resets
        (
            user_id,
            token_hash,
            expires_at
        )
        VALUES (?, ?, ?)
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $userId,
        $tokenHash,
        $expiresAt
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    if (!$result) {
        return false;
    }

    return $token;
}


/*
--------------------------------------------------
HÀM: getUserByResetToken()
--------------------------------------------------
*/
function getUserByResetToken($conn, $token)
{
    $tokenHash = hash(
        "sha256",
        $token
    );

    $sql = "
        SELECT
            u.*,
            pr.id AS reset_id,
            pr.expires_at
        FROM password_resets pr
        INNER JOIN users u
            ON u.id = pr.user_id
        WHERE pr.token_hash = ?
          AND pr.expires_at > NOW()
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $tokenHash
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $user ?: null;
}


/*
--------------------------------------------------
HÀM: updatePassword()
--------------------------------------------------
*/
function updatePassword($conn, $userId, $newPassword)
{
    $passwordHash = password_hash(
        $newPassword,
        PASSWORD_DEFAULT
    );

    $sql = "
        UPDATE users
        SET password = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $passwordHash,
        $userId
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}


/*
--------------------------------------------------
HÀM: deletePasswordResetToken()
--------------------------------------------------
*/
function deletePasswordResetToken($conn, $resetId)
{
    $sql = "
        DELETE FROM password_resets
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $resetId
    );

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $result;
}