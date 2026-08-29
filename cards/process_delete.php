<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: process_delete.php

MỤC ĐÍCH:
- Nhận card_id từ cards/index.php
- Yêu cầu đăng nhập
- Tìm Card cần xóa
- Tìm bộ Flashcard chứa Card
- Kiểm tra người dùng có phải chủ sở hữu không
- Gọi deleteCard()

NHẬN DỮ LIỆU:
- method: POST
- card_id

LUỒNG:
cards/index.php
      ↓
bấm "Xóa Flashcard"
      ↓ POST
process_delete.php
      ↓
requireLogin()
      ↓
getCardById()
      ↓
getSetById()
      ↓
kiểm tra owner
      ↓
deleteCard()
      ↓
DELETE cards
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


// 3. Functions của Cards.
require_once __DIR__ . "/functions.php";


// 4. Functions của Sets để kiểm tra owner.
require_once __DIR__ . "/../sets/functions.php";


// 5. Chỉ xử lý POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 6. Lấy ID Card.
    $cardId = intval(
        $_POST["card_id"] ?? 0
    );


    if ($cardId <= 0) {
        die("ID Flashcard không hợp lệ.");
    }


    // 7. Tìm Card.
    $card = getCardById(
        $conn,
        $cardId
    );


    if ($card == null) {
        die("Không tìm thấy Flashcard.");
    }


    /*
    Lưu set_id trước khi xóa.

    Vì sau khi DELETE Card,
    chúng ta vẫn cần biết phải quay về bộ nào.
    */
    $setId = $card["set_id"];


    // 8. Tìm bộ Flashcard chứa Card.
    $set = getSetById(
        $conn,
        $setId
    );


    if ($set == null) {
        die("Không tìm thấy bộ Flashcard.");
    }


    // 9. Kiểm tra chủ sở hữu.
    if ($set["user_id"] != $_SESSION["user_id"]) {
        die("Bạn không có quyền xóa Flashcard này.");
    }


    // 10. Xóa Card.
    $result = deleteCard(
        $conn,
        $cardId
    );


    if (!$result) {
        die("Xóa Flashcard thất bại.");
    }


    // 11. Quay lại đúng bộ Flashcard.
    header(
        "Location: /PRJ_FLASHCARD/cards/index.php?set_id="
        . $setId
    );

    exit();
}