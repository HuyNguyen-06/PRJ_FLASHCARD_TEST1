<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: index.php

MỤC ĐÍCH:
- Hiển thị các Card thuộc một bộ Flashcard

NHẬN DỮ LIỆU:
- set_id từ URL

Ví dụ:
cards/index.php?set_id=4

QUYỀN TRUY CẬP:
- Public + Approved:
  Guest/User/Admin đều xem được

- Private/Pending/Rejected:
  Chỉ chủ sở hữu hoặc Admin xem được

LƯU Ý:
- KHÔNG requireLogin() cho toàn trang
- Vì Card của bộ public đã duyệt
  phải cho Guest xem được
==================================================
*/


// 1. Kết nối database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 2. Session để biết người đang truy cập là ai.
// Không gọi requireLogin() toàn trang.
require_once __DIR__ . "/../config/auth.php";


// 3. Hàm của module Cards.
require_once __DIR__ . "/functions.php";


// 4. Dùng getSetById() của module Sets
// để lấy thông tin bộ Flashcard.
require_once __DIR__ . "/../sets/functions.php";


// 5. Lấy set_id từ URL.
$setId = intval($_GET["set_id"] ?? 0);


if ($setId <= 0) {
    die("ID bộ Flashcard không hợp lệ.");
}


// 6. Kiểm tra bộ Flashcard có tồn tại không.
$set = getSetById(
    $conn,
    $setId
);


if ($set == null) {
    die("Không tìm thấy bộ Flashcard.");
}


/*
7. Kiểm tra quyền xem bộ thẻ.
*/

$isPublicApproved =
    $set["visibility"] == "public"
    &&
    $set["status"] == "approved";


$isOwner =
    isLoggedIn()
    &&
    $_SESSION["user_id"] == $set["user_id"];


$isAdminUser = isAdmin();


if (!$isPublicApproved) {

    if (!isLoggedIn()) {

        header(
            "Location: /PRJ_FLASHCARD/auth/login.php"
        );

        exit();
    }


    if (!$isOwner && !$isAdminUser) {

        die(
            "Bạn không có quyền xem các Flashcard của bộ này."
        );
    }
}


// 8. Lấy danh sách Card.
$cards = getCardsBySet(
    $conn,
    $setId
);


// 9. Sau khi xử lý PHP mới hiển thị HTML.
include __DIR__ . "/../includes/header.php";

?>

<h1>
    <?php echo htmlspecialchars($set["title"]); ?>
</h1>
<?php if (mysqli_num_rows($cards) > 0): ?>

    <p>
        <a href="study.php?set_id=<?php echo $setId; ?>">
            Bắt đầu học
        </a>
    </p>

<?php endif; ?>
<?php if ($isOwner): ?>

    <p>
        <a href="create.php?set_id=<?php echo $setId; ?>">
            Thêm Flashcard
        </a>
    </p>

<?php endif; ?>
<p>
    Danh sách câu hỏi và đáp án
</p>


<?php if (mysqli_num_rows($cards) > 0): ?>

    <?php while ($card = mysqli_fetch_assoc($cards)): ?>

        <div class="flashcard">

            <h3>
                Câu hỏi
            </h3>

            <p>
                <?php
                echo htmlspecialchars(
                    $card["question"]
                );
                ?>
            </p>


            <h3>
                Đáp án
            </h3>

            <p>
                <?php
                echo htmlspecialchars(
                    $card["answer"]
                );
                ?>
            </p>
            <?php if ($isOwner): ?>

                <p>
                    <a href="edit.php?card_id=<?php echo $card["id"]; ?>">
                        Sửa Flashcard
                    </a>
                </p>


                <!--
                BƯỚC 10.4:
                Chỉ chủ sở hữu bộ thẻ mới thấy nút Xóa.

                Khi bấm:
                - gửi card_id
                - dùng POST
                - process_delete.php sẽ kiểm tra quyền lại
                -->
                <form
                    action="process_delete.php"
                    method="POST"
                    onsubmit="return confirm('Bạn có chắc muốn xóa Flashcard này không?');"
                >

                    <input
                        type="hidden"
                        name="card_id"
                        value="<?php echo $card["id"]; ?>"
                    >

                    <button type="submit">
                        Xóa Flashcard
                    </button>

                </form>

            <?php endif; ?>

        </div>

        <hr>

    <?php endwhile; ?>


<?php else: ?>

    <p>
        Bộ Flashcard này chưa có câu hỏi nào.
    </p>

<?php endif; ?>


<p>
    <a href="/PRJ_FLASHCARD/sets/detail.php?id=<?php echo $setId; ?>">
        Quay lại bộ Flashcard
    </a>
</p>


<?php

include __DIR__ . "/../includes/footer.php";

?>