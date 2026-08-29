<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: edit.php

MỤC ĐÍCH:
- Hiển thị form sửa Question / Answer
- Chỉ chủ sở hữu bộ Flashcard được sửa Card

NHẬN DỮ LIỆU:
- card_id từ URL

Ví dụ:
edit.php?card_id=2

QUYỀN:
- Guest      : KHÔNG
- User khác  : KHÔNG
- Chủ bộ thẻ : ĐƯỢC

LUỒNG:
cards/index.php
       ↓
edit.php?card_id=...
       ↓
requireLogin()
       ↓
getCardById()
       ↓
getSetById()
       ↓
kiểm tra owner
       ↓
form sửa
       ↓ POST
process_update.php
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Kết nối database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Hàm Cards.
require_once __DIR__ . "/functions.php";


// 4. Hàm Sets để kiểm tra chủ sở hữu.
require_once __DIR__ . "/../sets/functions.php";


// 5. Lấy card_id từ URL.
$cardId = intval($_GET["card_id"] ?? 0);


if ($cardId <= 0) {
    die("ID Flashcard không hợp lệ.");
}


// 6. Lấy Card.
$card = getCardById(
    $conn,
    $cardId
);


if ($card == null) {
    die("Không tìm thấy Flashcard.");
}


// 7. Lấy bộ Flashcard chứa Card này.
$set = getSetById(
    $conn,
    $card["set_id"]
);


if ($set == null) {
    die("Không tìm thấy bộ Flashcard.");
}


// 8. Chỉ chủ bộ thẻ được sửa.
if ($set["user_id"] != $_SESSION["user_id"]) {
    die("Bạn không có quyền sửa Flashcard này.");
}


// 9. Hiển thị giao diện.
include __DIR__ . "/../includes/header.php";

?>

<h1>Sửa Flashcard</h1>

<p>
    Bộ:
    <strong>
        <?php echo htmlspecialchars($set["title"]); ?>
    </strong>
</p>


<form action="process_update.php" method="POST">

    <input
        type="hidden"
        name="card_id"
        value="<?php echo $card["id"]; ?>"
    >


    <div>

        <label for="question">
            Câu hỏi
        </label>

        <textarea
            id="question"
            name="question"
            rows="5"
            required
        ><?php echo htmlspecialchars($card["question"]); ?></textarea>

    </div>


    <div>

        <label for="answer">
            Đáp án
        </label>

        <textarea
            id="answer"
            name="answer"
            rows="5"
            required
        ><?php echo htmlspecialchars($card["answer"]); ?></textarea>

    </div>


    <button type="submit">
        Lưu thay đổi
    </button>

</form>


<p>
    <a href="index.php?set_id=<?php echo $set["id"]; ?>">
        Hủy và quay lại
    </a>
</p>


<?php

include __DIR__ . "/../includes/footer.php";

?>