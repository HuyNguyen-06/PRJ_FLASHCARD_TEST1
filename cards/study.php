<?php

/*
==================================================
MODULE: QUẢN LÝ & HỌC FLASHCARD
OWNER: Lê Mai Thiện Độ

FILE: study.php

MỤC ĐÍCH:
- Hiển thị từng Flashcard để người dùng học
- Cho xem Question trước
- Người dùng bấm "Xem đáp án"
- Sau đó chọn:
    + Biết
    + Chưa biết
- JavaScript sẽ đếm kết quả phiên học

NHẬN DỮ LIỆU:
- set_id từ URL

Ví dụ:
study.php?set_id=4

QUYỀN:
- Bộ public + approved:
  Guest/User/Admin đều được học

- Bộ private/pending/rejected:
  chỉ chủ sở hữu hoặc Admin được học

LƯU Ý:
- KHÔNG requireLogin() toàn trang
- Guest được học nội dung public
- Việc lưu tiến độ sẽ thuộc module progress/

CẬP NHẬT (Lê Mai Thiện Độ) — hoàn thành phần "Cần làm tiếp":
- UI Study đẹp hơn: flip card (lật câu hỏi/đáp án) + progress bar.
- Progress indicator: thanh tiến độ trực quan bên cạnh "Câu x/y".
- 0 Card: không còn die() thô, hiển thị màn hình thân thiện có
  link quay lại thay vì trang trắng.
- 1 Card / nhiều Card: đã kiểm tra qua luồng nextCard() trong
  study.js, không phát sinh lỗi vòng lặp.
- Chống double-click: xử lý ở study.js (khóa nút ngay khi bấm).
==================================================
*/


// 1. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 2. Session để kiểm tra quyền.
// Không requireLogin() toàn trang.
require_once __DIR__ . "/../config/auth.php";


// 3. Functions Cards.
require_once __DIR__ . "/functions.php";


// 4. Functions Sets.
require_once __DIR__ . "/../sets/functions.php";


// 5. Lấy set_id.
$setId = intval($_GET["set_id"] ?? 0);


if ($setId <= 0) {
    die("ID bộ Flashcard không hợp lệ.");
}


// 6. Lấy thông tin bộ thẻ.
$set = getSetById(
    $conn,
    $setId
);


if ($set == null) {
    die("Không tìm thấy bộ Flashcard.");
}


/*
7. Kiểm tra quyền xem/học.

Public + Approved:
-> ai cũng được học.

Các trạng thái còn lại:
-> chỉ Owner hoặc Admin.
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
            "Bạn không có quyền học bộ Flashcard này."
        );
    }
}


// 8. Lấy Card trong bộ.
$result = getCardsBySet(
    $conn,
    $setId
);


// 9. Chuyển kết quả MySQL thành Array PHP.
//
// Vì lát nữa JavaScript cần toàn bộ Card
// để chuyển câu mà không reload trang.
$cards = [];


while ($card = mysqli_fetch_assoc($result)) {

    $cards[] = [
        "id" => $card["id"],
        "question" => $card["question"],
        "answer" => $card["answer"]
    ];
}


// Không có Card thì không thể học.
//
// Trước đây dùng die() -> trang trắng, không thân thiện.
// Bây giờ hiển thị màn hình có Header/Footer + link quay lại.
$hasNoCards = (count($cards) == 0);


// 10. Header chung.
include __DIR__ . "/../includes/header.php";

?>

<?php if ($hasNoCards): ?>

    <h1>
        Học:
        <?php echo htmlspecialchars($set["title"]); ?>
    </h1>

    <div class="study-empty">

        <p>
            Bộ Flashcard này chưa có câu hỏi nào để học.
        </p>

        <?php if ($isOwner): ?>

            <p>
                <a href="create.php?set_id=<?php echo $setId; ?>">
                    Thêm Flashcard ngay
                </a>
            </p>

        <?php endif; ?>

        <p>
            <a href="index.php?set_id=<?php echo $setId; ?>">
                Quay lại danh sách Flashcard
            </a>
        </p>

    </div>

<?php

    // Dừng luôn tại đây, không render phần Study bên dưới.
    include __DIR__ . "/../includes/footer.php";

    exit();

endif;

?>

<h1>
    Học:
    <?php echo htmlspecialchars($set["title"]); ?>
</h1>


<div class="study-wrapper">

    <!--
    Progress indicator:
    vừa có thanh tiến độ (thanh %) vừa có chữ "Câu x / y"
    để người học vừa thấy tổng quan vừa thấy con số cụ thể.
    -->
    <div class="study-progress-bar">

        <div
            id="study-progress-fill"
            class="study-progress-fill"
        ></div>

    </div>

    <p id="study-progress">
        Câu 1 / <?php echo count($cards); ?>
    </p>


    <!--
    Flip card:
    - Bấm vào thẻ (hoặc nút "Xem đáp án") sẽ lật thẻ từ
      Câu hỏi (mặt trước) sang Đáp án (mặt sau) bằng CSS.
    - study.js thêm/bớt class "is-flipped" trên #flip-card
      để kích hoạt hiệu ứng lật.
    -->
    <div
        class="flip-card"
        id="flip-card"
    >

        <div
            class="flip-card-inner"
            id="flip-card-inner"
        >

            <div class="flip-card-face flip-card-front">

                <span class="flip-card-label">Câu hỏi</span>

                <p id="study-question"></p>

                <span class="flip-card-hint">
                    Bấm vào thẻ để xem đáp án
                </span>

            </div>

            <div class="flip-card-face flip-card-back">

                <span class="flip-card-label">Đáp án</span>

                <p id="study-answer"></p>

            </div>

        </div>

    </div>


    <div class="study-actions">

        <button
            type="button"
            id="show-answer-btn"
            class="study-btn study-btn-primary"
        >
            Xem đáp án
        </button>


        <!--
        Hai nút này ban đầu ẩn.

        Chỉ sau khi xem đáp án
        mới cho người học tự đánh giá.

        study.js sẽ khóa (disabled) hai nút này ngay khi
        người dùng bấm, để chống double-click làm sai lệch
        kết quả đếm correct/wrong.
        -->
        <div
            id="result-buttons"
            class="study-result-buttons"
            style="display: none;"
        >

            <button
                type="button"
                id="correct-btn"
                class="study-btn study-btn-correct"
            >
                Biết
            </button>

            <button
                type="button"
                id="wrong-btn"
                class="study-btn study-btn-wrong"
            >
                Chưa biết
            </button>

        </div>

    </div>

</div>


<!--
Khu vực này chỉ xuất hiện
sau khi học hết tất cả Card.
-->
<div
    id="study-result"
    class="study-result"
    style="display: none;"
>

    <h2>Hoàn thành phiên học</h2>

    <p>
        Tổng số:
        <strong id="total-result"></strong>
    </p>

    <p>
        Biết:
        <strong id="correct-result"></strong>
    </p>

    <p>
        Chưa biết:
        <strong id="wrong-result"></strong>
    </p>

    <p>
        Phần trăm:
        <strong id="percent-result"></strong>%
    </p>

    <p id="save-message"></p>

</div>


<p>
    <a href="index.php?set_id=<?php echo $setId; ?>">
        Quay lại danh sách Flashcard
    </a>
</p>


<!--
Chuyển Array PHP thành JavaScript.

Ví dụ PHP:
[
    ["question" => "...", "answer" => "..."]
]

sẽ được chuyển thành Array JavaScript.
-->
<script>

const studyCards = <?php echo json_encode(
    $cards,
    JSON_UNESCAPED_UNICODE
); ?>;

const studySetId = <?php echo $setId; ?>;

/*
Cho JavaScript biết:
người học hiện tại đã đăng nhập hay chưa.
*/
const studyIsLoggedIn =
    <?php echo isLoggedIn() ? "true" : "false"; ?>;


</script>


<script src="/PRJ_FLASHCARD/assets/js/study.js"></script>


<?php

include __DIR__ . "/../includes/footer.php";

?>