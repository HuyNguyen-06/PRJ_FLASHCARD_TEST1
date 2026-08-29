<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN
OWNER: Trần Đăng Khoa

FILE: functions.php

MỤC ĐÍCH:
- Chứa các hàm dành cho Profile và Admin
- Quản lý người dùng
- Duyệt bộ Flashcard công khai
- Thống kê cho Admin

DATABASE CÓ THỂ SỬ DỤNG:
- users
- flashcard_sets
- study_history

LƯU Ý:
- Admin KHÔNG tạo bộ Flashcard tại đây
- Admin chỉ duyệt / từ chối bộ do module Sets tạo
==================================================
*/


/*
--------------------------------------------------
HÀM: getPendingSets()

Mục đích:
Lấy các bộ Flashcard công khai đang chờ Admin duyệt.

Điều kiện:
- visibility = public
- status = pending

Input:
- $conn : kết nối database

Output:
- Danh sách các bộ Flashcard đang chờ duyệt
--------------------------------------------------
*/
function getPendingSets($conn)
{
    $sql = "
        SELECT flashcard_sets.*, users.name AS owner_name
        FROM flashcard_sets

        JOIN users
        ON flashcard_sets.user_id = users.id

        WHERE flashcard_sets.visibility = 'public'
        AND flashcard_sets.status = 'pending'

        ORDER BY flashcard_sets.created_at DESC
    ";

    return mysqli_query($conn, $sql);
}
/*
--------------------------------------------------
HÀM: approveSet()

Mục đích:
- Admin duyệt một bộ Flashcard đang chờ duyệt
- Chuyển status từ pending thành approved

Input:
- $conn  : kết nối database
- $setId : ID của bộ Flashcard cần duyệt

Output:
- true  : duyệt thành công
- false : không duyệt được

LƯU Ý:
- Chỉ xử lý bộ public
- Chỉ xử lý bộ đang có status = pending
--------------------------------------------------
*/
function approveSet($conn, $setId)
{
    $sql = "
        UPDATE flashcard_sets
        SET status = 'approved'
        WHERE id = ?
        AND visibility = 'public'
        AND status = 'pending'
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $setId
    );

    // Nếu câu SQL chạy thất bại.
    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }

    /*
    affected_rows > 0 nghĩa là:
    thật sự có một bộ thẻ được thay đổi.
    */
    return mysqli_stmt_affected_rows($stmt) > 0;
}
/*
--------------------------------------------------
HÀM: rejectSet()

Mục đích:
- Admin từ chối một bộ Flashcard đang chờ duyệt
- Chuyển status từ pending thành rejected

Input:
- $conn  : kết nối database
- $setId : ID bộ Flashcard cần từ chối

Output:
- true  : từ chối thành công
- false : không thể từ chối

LƯU Ý:
- Chỉ xử lý bộ public
- Chỉ xử lý bộ đang ở trạng thái pending
--------------------------------------------------
*/
function rejectSet($conn, $setId)
{
    $sql = "
        UPDATE flashcard_sets
        SET status = 'rejected'
        WHERE id = ?
        AND visibility = 'public'
        AND status = 'pending'
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $setId
    );

    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }

    return mysqli_stmt_affected_rows($stmt) > 0;
}
/*
--------------------------------------------------
HÀM: getUserProfile()

Mục đích:
- Lấy thông tin hồ sơ của một user

Input:
- $conn
- $userId

Output:
- Thông tin user nếu tìm thấy
- null nếu không tìm thấy
--------------------------------------------------
*/
function getUserProfile($conn, $userId)
{
    $sql = "
        SELECT
            id,
            name,
            email,
            role,
            interests,
            theme,
            notif_email,
            created_at
        FROM users
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    if ($user) {
        return $user;
    }

    return null;
}


/*
--------------------------------------------------
HÀM: updateUserProfile()

Mục đích:
- Cập nhật hồ sơ user

Cho phép sửa:
- name
- email
- interests

KHÔNG sửa:
- role
- password
--------------------------------------------------
*/
function updateUserProfile(
    $conn,
    $userId,
    $name,
    $email,
    $interests
) {
    $sql = "
        UPDATE users
        SET
            name = ?,
            email = ?,
            interests = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $name,
        $email,
        $interests,
        $userId
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: changeUserPassword()

Mục đích:
- Đổi mật khẩu của user hiện tại

Input:
- $conn
- $userId
- $newPassword

Output:
- true  : đổi thành công
- false : thất bại

LƯU Ý:
- Mật khẩu mới phải được password_hash()
  trước khi lưu vào database
--------------------------------------------------
*/
function changeUserPassword(
    $conn,
    $userId,
    $newPassword
) {
    $passwordHash = password_hash(
        $newPassword,
        PASSWORD_DEFAULT
    );

    $sql = "
        UPDATE users
        SET password = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $passwordHash,
        $userId
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: updateUserSettings()

Mục đích:
- Cập nhật cài đặt cá nhân

Cho phép:
- theme
- notif_email
--------------------------------------------------
*/
function updateUserSettings(
    $conn,
    $userId,
    $theme,
    $notifEmail
) {
    $sql = "
        UPDATE users
        SET
            theme = ?,
            notif_email = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sii",
        $theme,
        $notifEmail,
        $userId
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: getAllUsers()

Mục đích:
- Admin lấy danh sách tất cả user
--------------------------------------------------
*/
function getAllUsers($conn)
{
    $sql = "
        SELECT
            id,
            name,
            email,
            role,
            created_at
        FROM users
        ORDER BY created_at DESC
    ";

    return mysqli_query($conn, $sql);
}


/*
--------------------------------------------------
HÀM: getUserById()

Mục đích:
- Admin xem chi tiết một user
--------------------------------------------------
*/
function getUserById($conn, $userId)
{
    $sql = "
        SELECT
            id,
            name,
            email,
            role,
            interests,
            theme,
            notif_email,
            created_at
        FROM users
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    return $user ?: null;
}


/*
--------------------------------------------------
HÀM: deleteUser()

Mục đích:
- Xóa tài khoản user

LƯU Ý:
- Không dùng hàm này để Admin tự xóa chính mình
--------------------------------------------------
*/
function deleteUser($conn, $userId)
{
    $sql = "
        DELETE FROM users
        WHERE id = ?
        AND role != 'admin'
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }

    return mysqli_stmt_affected_rows($stmt) > 0;
}
/*
--------------------------------------------------
HÀM: getAdminStatistics()

Mục đích:
- Lấy số liệu tổng quan cho Admin Dashboard
--------------------------------------------------
*/
function getAdminStatistics($conn)
{
    $statistics = [];


    // Tổng user.
    $result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM users"
    );

    $row = mysqli_fetch_assoc($result);

    $statistics["total_users"] =
        $row["total"];


    // Tổng bộ Flashcard.
    $result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM flashcard_sets"
    );

    $row = mysqli_fetch_assoc($result);

    $statistics["total_sets"] =
        $row["total"];


    // Bộ đang chờ duyệt.
    $result = mysqli_query(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM flashcard_sets
        WHERE visibility = 'public'
        AND status = 'pending'
        "
    );

    $row = mysqli_fetch_assoc($result);

    $statistics["pending_sets"] =
        $row["total"];


    // Tổng phiên học.
    $result = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM study_history"
    );

    $row = mysqli_fetch_assoc($result);

    $statistics["study_sessions"] =
        $row["total"];


    return $statistics;
}
