<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: create.php

MỤC ĐÍCH:
- Hiển thị form thêm Flashcard
- Một Flashcard gồm:
    + question
    + answer
- Card phải thuộc một bộ Flashcard cụ thể

NHẬN DỮ LIỆU:
- set_id từ URL

Ví dụ:
create.php?set_id=3

QUYỀN:
- Guest      : KHÔNG
- User khác  : KHÔNG
- Chủ bộ thẻ : ĐƯỢC

LUỒNG:
cards/index.php
      ↓
create.php?set_id=...
      ↓
requireLogin()
      ↓
kiểm tra owner
      ↓
form Question / Answer
      ↓ POST
process_create.php
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Kết nối database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Dùng hàm của Sets để kiểm tra bộ thẻ.
require_once __DIR__ . "/../sets/functions.php";


// 4. Lấy set_id từ URL.
$setId = intval($_GET["set_id"] ?? 0);


if ($setId <= 0) {
    die("ID bộ Flashcard không hợp lệ.");
}


// 5. Lấy thông tin bộ Flashcard.
$set = getSetById(
    $conn,
    $setId
);


if ($set == null) {
    die("Không tìm thấy bộ Flashcard.");
}


// 6. Chỉ chủ sở hữu mới được thêm Card.
if ($set["user_id"] != $_SESSION["user_id"]) {
    die("Bạn không có quyền thêm Flashcard vào bộ này.");
}


// 7. Hiển thị giao diện.
include __DIR__ . "/../includes/header.php";

?>

<h1>Thêm Flashcard</h1>

<p>
    Bộ:
    <strong>
        <?php echo htmlspecialchars($set["title"]); ?>
    </strong>
</p>


<form action="process_create.php" method="POST">

    <!--
    Cho process_create.php biết
    Card này thuộc bộ Flashcard nào.
    -->
    <input
        type="hidden"
        name="set_id"
        value="<?php echo $set["id"]; ?>"
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
        ></textarea>

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
        ></textarea>

    </div>


    <button type="submit">
        Thêm Flashcard
    </button>

</form>


<p>
    <a href="index.php?set_id=<?php echo $setId; ?>">
        Quay lại danh sách Flashcard
    </a>
</p>


<?php

include __DIR__ . "/../includes/footer.php";

?>