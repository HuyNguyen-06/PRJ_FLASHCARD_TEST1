<?php

/*
==================================================
MODULE: KẾT QUẢ & TIẾN ĐỘ
OWNER: Phạm Tiến Đạt

FILE: result.php

MỤC ĐÍCH:
- Hiển thị kết quả phiên học VỪA HOÀN THÀNH
- Nhận set_id qua URL: result.php?set_id=5
- Lấy lại kết quả từ database (KHÔNG tin số liệu
  truyền qua URL), tránh người dùng tự sửa
  total/correct/wrong trên thanh địa chỉ

LUỒNG:
cards/study.php học xong
    ↓ fetch() POST
progress/save_result.php lưu study_history
    ↓
cards/study.php chuyển hướng:
    result.php?set_id=<setId>
    ↓
result.php
    ↓
getLatestResultBySet()
    ↓
hiển thị kết quả phiên gần nhất

QUYỀN:
- Phải đăng nhập
- Chỉ xem được kết quả của CHÍNH MÌNH
  (userId luôn lấy từ Session, không lấy từ URL)

LƯU Ý CHO MODULE CARDS (Lê Mai Thiện Độ):
- Sau khi save_result.php trả về success = true,
  study.php nên điều hướng người dùng sang:
  /PRJ_FLASHCARD/progress/result.php?set_id=<setId>
  để họ thấy kết quả rõ ràng thay vì ở nguyên
  trang học.
==================================================
*/


// 1. Yêu cầu đăng nhập.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Functions Progress.
require_once __DIR__ . "/functions.php";


// 4. userId luôn lấy từ Session, không tin dữ liệu ngoài.
$userId = $_SESSION["user_id"];


// 5. setId lấy từ URL, chỉ dùng để TRUY VẤN, không dùng để hiển thị số liệu trực tiếp.
$setId = intval($_GET["set_id"] ?? 0);


// 6. Nếu có set_id hợp lệ thì tìm kết quả gần nhất.
$latestResult = null;

if ($setId > 0) {

    $latestResult = getLatestResultBySet(
        $conn,
        $userId,
        $setId
    );
}


// 7. Giao diện chung.
include __DIR__ . "/../includes/header.php";

?>

<style>
    /*
    Style scoped riêng cho trang result.php,
    KHÔNG chỉnh sửa assets/css/style.css (file dùng chung)
    để tránh ảnh hưởng module khác khi chưa báo nhóm.
    */
    .progress-result-card {
        max-width: 480px;
        margin: 0 auto;
        padding: 24px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        text-align: center;
    }

    .progress-result-percent {
        font-size: 48px;
        font-weight: bold;
        margin: 10px 0;
    }

    .progress-result-grid {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
    }

    .progress-result-grid div {
        text-align: center;
    }

    .progress-result-grid h3 {
        margin: 0;
        font-size: 24px;
    }

    .progress-result-grid p {
        margin: 4px 0 0;
        color: #666;
        font-size: 13px;
    }

    .progress-result-actions {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .progress-empty-box {
        max-width: 480px;
        margin: 0 auto;
        padding: 24px;
        text-align: center;
    }
</style>

<h1>Kết quả học tập</h1>

<?php if ($latestResult): ?>

    <div class="progress-result-card">

        <p>
            Bộ Flashcard:
            <strong>
                <?php
                echo htmlspecialchars(
                    $latestResult["set_title"]
                );
                ?>
            </strong>
        </p>

        <div class="progress-result-percent">
            <?php echo $latestResult["percent"]; ?>%
        </div>

        <div class="progress-result-grid">

            <div>
                <h3><?php echo $latestResult["total"]; ?></h3>
                <p>Tổng câu</p>
            </div>

            <div>
                <h3><?php echo $latestResult["correct"]; ?></h3>
                <p>Đã biết</p>
            </div>

            <div>
                <h3><?php echo $latestResult["wrong"]; ?></h3>
                <p>Chưa biết</p>
            </div>

        </div>

        <p>
            Thời gian:
            <?php
            echo htmlspecialchars(
                formatDateTime($latestResult["created_at"])
            );
            ?>
        </p>

        <div class="progress-result-actions">

            <a href="/PRJ_FLASHCARD/cards/study.php?set_id=<?php echo $setId; ?>">
                Học lại bộ này
            </a>

            <a href="history.php">
                Xem lịch sử học
            </a>

            <a href="statistics.php">
                Xem thống kê
            </a>

        </div>

    </div>

<?php else: ?>

    <!--
    Không có set_id hoặc chưa từng học bộ này.
    Vẫn giữ hành vi cũ: điều hướng sang History/Statistics.
    -->
    <div class="progress-empty-box">

        <p>
            Chưa có kết quả nào để hiển thị.
        </p>

        <p>
            <a href="history.php">
                Xem lịch sử học
            </a>
        </p>

        <p>
            <a href="statistics.php">
                Xem thống kê
            </a>
        </p>

    </div>

<?php endif; ?>

<?php

include __DIR__ . "/../includes/footer.php";

?>
