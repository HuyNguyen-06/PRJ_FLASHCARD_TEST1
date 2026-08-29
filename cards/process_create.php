<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: process_create.php

MỤC ĐÍCH:
- Nhận Question và Answer từ create.php
- Kiểm tra người dùng đã đăng nhập
- Kiểm tra bộ Flashcard thuộc người dùng
- Gọi createCard()
- INSERT dữ liệu vào bảng cards

NHẬN DỮ LIỆU:
- set_id
- question
- answer

LUỒNG:
create.php
    ↓ POST
process_create.php
    ↓
requireLogin()
    ↓
kiểm tra owner
    ↓
createCard()
    ↓
cards
    ↓
cards/index.php
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Kết nối database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Hàm của Cards.
require_once __DIR__ . "/functions.php";


// 4. Hàm của Sets để kiểm tra owner.
require_once __DIR__ . "/../sets/functions.php";


// 5. Chỉ xử lý POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 6. Lấy dữ liệu.
    $setId = intval($_POST["set_id"] ?? 0);

    $question = trim(
        $_POST["question"] ?? ""
    );

    $answer = trim(
        $_POST["answer"] ?? ""
    );


    // 7. Kiểm tra dữ liệu.
    if ($setId <= 0) {
        die("ID bộ Flashcard không hợp lệ.");
    }


    if ($question == "") {
        die("Câu hỏi không được để trống.");
    }


    if ($answer == "") {
        die("Đáp án không được để trống.");
    }


    /*
    8. Kiểm tra bộ Flashcard tồn tại
    và thuộc người đang đăng nhập.
    */
    $set = getSetById(
        $conn,
        $setId
    );


    if ($set == null) {
        die("Không tìm thấy bộ Flashcard.");
    }


    if ($set["user_id"] != $_SESSION["user_id"]) {
        die("Bạn không có quyền thêm Flashcard vào bộ này.");
    }


    // 9. INSERT Card.
    $result = createCard(
        $conn,
        $setId,
        $question,
        $answer
    );


    if (!$result) {
        die("Thêm Flashcard thất bại.");
    }


    // 10. Thành công -> quay lại danh sách Card.
    header(
        "Location: /PRJ_FLASHCARD/cards/index.php?set_id=" . $setId
    );

    exit();
}