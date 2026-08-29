<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN
OWNER: Trần Đăng Khoa

FILE: admin/process_reject_set.php

MỤC ĐÍCH:
- Nhận set_id từ pending_sets.php
- Chỉ cho Admin thực hiện
- Gọi rejectSet()
- Chuyển trạng thái:
    pending -> rejected

NHẬN DỮ LIỆU:
- method: POST
- set_id

LUỒNG:
pending_sets.php
        ↓
Admin bấm "Từ chối"
        ↓ POST
process_reject_set.php
        ↓
requireAdmin()
        ↓
rejectSet()
        ↓
status = rejected
        ↓
quay lại pending_sets.php
==================================================
*/


// 1. Kiểm tra quyền Admin.
require_once __DIR__ . "/../../config/auth.php";

requireAdmin();


// 2. Kết nối database.
require_once __DIR__ . "/../../config/database.php";

/** @var mysqli $conn */


// 3. Gọi các hàm của module User/Admin.
require_once __DIR__ . "/../functions.php";


// 4. Chỉ xử lý POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 5. Lấy ID bộ Flashcard cần từ chối.
    $setId = intval($_POST["set_id"] ?? 0);


    // 6. Kiểm tra ID hợp lệ.
    if ($setId <= 0) {
        die("ID bộ Flashcard không hợp lệ.");
    }


    // 7. Gọi hàm từ chối.
    $result = rejectSet(
        $conn,
        $setId
    );


    // 8. Nếu thất bại.
    if (!$result) {
        die("Không thể từ chối bộ Flashcard.");
    }


    // 9. Thành công -> quay lại danh sách chờ duyệt.
    header(
        "Location: /PRJ_FLASHCARD/user_admin/admin/pending_sets.php"
    );

    exit();
}