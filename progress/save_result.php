<?php

/*
==================================================
MODULE: KẾT QUẢ & TIẾN ĐỘ
OWNER: Phạm Tiến Đạt

FILE: save_result.php

MỤC ĐÍCH:
- Nhận kết quả phiên học từ study.js bằng fetch()
- Chỉ lưu khi người dùng đã đăng nhập
- Lấy user_id từ Session
- Tính percent
- INSERT vào study_history

NHẬN DỮ LIỆU:
- set_id
- total
- correct
- wrong

TRẢ VỀ:
- JSON cho JavaScript
==================================================
*/


// 1. Session chung.
// Không dùng requireLogin() vì đây là endpoint nhận fetch().
require_once __DIR__ . "/../config/auth.php";


// 2. Kết quả trả về cho JavaScript là JSON.
header("Content-Type: application/json");


// 3. Nếu chưa đăng nhập -> không lưu.
if (!isLoggedIn()) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Bạn cần đăng nhập để lưu tiến độ."
    ]);

    exit();
}


// 4. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 5. Functions Progress.
require_once __DIR__ . "/functions.php";


// 6. Chỉ nhận POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = $_SESSION["user_id"];

    $setId = intval($_POST["set_id"] ?? 0);
    $total = intval($_POST["total"] ?? 0);
    $correct = intval($_POST["correct"] ?? 0);
    $wrong = intval($_POST["wrong"] ?? 0);


    // Kiểm tra dữ liệu.
    if ($setId <= 0 || $total <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Dữ liệu phiên học không hợp lệ."
        ]);

        exit();
    }


    if ($correct < 0 || $wrong < 0) {

        echo json_encode([
            "success" => false,
            "message" => "Kết quả học không hợp lệ."
        ]);

        exit();
    }


    if (($correct + $wrong) != $total) {

        echo json_encode([
            "success" => false,
            "message" => "Tổng kết quả không khớp."
        ]);

        exit();
    }


    /*
    6b. Chống double submit / refresh.

    Nếu đã có một bản ghi GIỐNG HỆT
    (cùng user, cùng set, cùng total/correct/wrong)
    được tạo trong 5 giây gần đây
    -> coi như đã lưu rồi, KHÔNG insert thêm lần nữa.

    Trường hợp xảy ra:
    - JS gọi fetch() 2 lần do double-click
    - Người dùng bấm F5 ngay sau khi học xong
      và trình duyệt gửi lại request cũ
    */
    if (
        hasDuplicateRecentResult(
            $conn,
            $userId,
            $setId,
            $total,
            $correct,
            $wrong
        )
    ) {
        echo json_encode([
            "success" => true,
            "duplicate" => true,
            "message" => "Kết quả này vừa được lưu trước đó."
        ]);

        exit();
    }


    // Tính phần trăm.
    $percent = round(
        ($correct / $total) * 100
    );


    // Lưu lịch sử.
    $result = saveStudyResult(
        $conn,
        $userId,
        $setId,
        $total,
        $correct,
        $wrong,
        $percent
    );


    if (!$result) {

        echo json_encode([
            "success" => false,
            "message" => "Không thể lưu kết quả học."
        ]);

        exit();
    }


    echo json_encode([
        "success" => true,
        "message" => "Đã lưu kết quả học."
    ]);

    exit();
}