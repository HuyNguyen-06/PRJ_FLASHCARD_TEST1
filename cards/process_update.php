<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: process_update.php

MỤC ĐÍCH:
- Nhận Question / Answer mới từ edit.php
- Kiểm tra quyền sở hữu
- UPDATE Card trong database

NHẬN DỮ LIỆU:
- card_id
- question
- answer

LUỒNG:
edit.php
    ↓ POST
process_update.php
    ↓
requireLogin()
    ↓
getCardById()
    ↓
getSetById()
    ↓
kiểm tra owner
    ↓
updateCard()
    ↓
cards/index.php
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Functions Cards.
require_once __DIR__ . "/functions.php";


// 4. Functions Sets.
require_once __DIR__ . "/../sets/functions.php";


// 5. Chỉ xử lý POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cardId = intval(
        $_POST["card_id"] ?? 0
    );

    $question = trim(
        $_POST["question"] ?? ""
    );

    $answer = trim(
        $_POST["answer"] ?? ""
    );


    // 6. Kiểm tra dữ liệu.
    if ($cardId <= 0) {
        die("ID Flashcard không hợp lệ.");
    }

    if ($question == "") {
        die("Câu hỏi không được để trống.");
    }

    if ($answer == "") {
        die("Đáp án không được để trống.");
    }


    // 7. Lấy Card hiện tại.
    $card = getCardById(
        $conn,
        $cardId
    );

    if ($card == null) {
        die("Không tìm thấy Flashcard.");
    }


    // 8. Lấy bộ chứa Card.
    $set = getSetById(
        $conn,
        $card["set_id"]
    );

    if ($set == null) {
        die("Không tìm thấy bộ Flashcard.");
    }


    // 9. Kiểm tra chủ sở hữu.
    if ($set["user_id"] != $_SESSION["user_id"]) {
        die("Bạn không có quyền sửa Flashcard này.");
    }


    // 10. UPDATE.
    $result = updateCard(
        $conn,
        $cardId,
        $question,
        $answer
    );


    if (!$result) {
        die("Cập nhật Flashcard thất bại.");
    }


    // 11. Quay lại danh sách Card.
    header(
        "Location: /PRJ_FLASHCARD/cards/index.php?set_id="
        . $card["set_id"]
    );

    exit();
}