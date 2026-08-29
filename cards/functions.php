<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: functions.php

MỤC ĐÍCH:
- Chứa các hàm xử lý Card
- Lấy Card theo bộ Flashcard
- Sau này tạo / sửa / xóa Card

DATABASE:
- Bảng cards

QUAN HỆ:
flashcard_sets.id
        ↓
cards.set_id

LƯU Ý:
- Module này KHÔNG tạo/xóa bộ Flashcard
- Bộ Flashcard thuộc trách nhiệm sets/
- Module này chỉ quản lý câu hỏi và đáp án
==================================================
*/


/*
--------------------------------------------------
HÀM: getCardsBySet()

Mục đích:
- Lấy tất cả Card thuộc một bộ Flashcard

Input:
- $conn  : kết nối database
- $setId : ID của bộ Flashcard

Output:
- Kết quả truy vấn chứa danh sách Card
--------------------------------------------------
*/
function getCardsBySet($conn, $setId)
{
    $sql = "
        SELECT *
        FROM cards
        WHERE set_id = ?
        ORDER BY id ASC
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $setId
    );

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}
/*
--------------------------------------------------
HÀM: createCard()

Mục đích:
- Tạo một Flashcard mới
- Flashcard thuộc một bộ thông qua set_id

Input:
- $conn     : kết nối database
- $setId    : ID bộ Flashcard
- $question : câu hỏi
- $answer   : đáp án

Output:
- true  : thêm thành công
- false : thêm thất bại

LƯU Ý:
- Hàm này chỉ INSERT vào bảng cards
- Quyền sở hữu sẽ được kiểm tra
  ở process_create.php
--------------------------------------------------
*/
function createCard(
    $conn,
    $setId,
    $question,
    $answer
) {
    $sql = "
        INSERT INTO cards
        (
            set_id,
            question,
            answer
        )
        VALUES (?, ?, ?)
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $setId,
        $question,
        $answer
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: getCardById()

Mục đích:
- Lấy thông tin một Card dựa vào ID

Input:
- $conn   : kết nối database
- $cardId : ID của Card

Output:
- Mảng thông tin Card nếu tồn tại
- null nếu không tìm thấy
--------------------------------------------------
*/
function getCardById($conn, $cardId)
{
    $sql = "
        SELECT *
        FROM cards
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $cardId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $card = mysqli_fetch_assoc($result);

    if ($card) {
        return $card;
    }

    return null;
}


/*
--------------------------------------------------
HÀM: updateCard()

Mục đích:
- Cập nhật Question và Answer của một Card

Input:
- $conn
- $cardId
- $question
- $answer

Output:
- true  : câu SQL chạy thành công
- false : thất bại

LƯU Ý:
- Quyền sở hữu được kiểm tra trước
  trong process_update.php
--------------------------------------------------
*/
function updateCard(
    $conn,
    $cardId,
    $question,
    $answer
) {
    $sql = "
        UPDATE cards
        SET question = ?,
            answer = ?
        WHERE id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $question,
        $answer,
        $cardId
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: deleteCard()

Mục đích:
- Xóa một Flashcard khỏi bảng cards

Input:
- $conn   : kết nối database
- $cardId : ID của Card cần xóa

Output:
- true  : xóa thành công
- false : không xóa được

LƯU Ý:
- Hàm này chỉ thực hiện DELETE
- Quyền sở hữu phải được kiểm tra
  trước khi gọi hàm
--------------------------------------------------
*/
function deleteCard($conn, $cardId)
{
    $sql = "
        DELETE FROM cards
        WHERE id = ?
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $cardId
    );

    if (!mysqli_stmt_execute($stmt)) {
        return false;
    }

    return mysqli_stmt_affected_rows($stmt) > 0;
}