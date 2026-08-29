<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN
OWNER: Trần Đăng Khoa

FILE: admin/process_approve_set.php

MỤC ĐÍCH:
- Nhận set_id từ pending_sets.php
- Kiểm tra người thực hiện phải là Admin
- Gọi approveSet()
- Chuyển bộ Flashcard:
    pending -> approved

NHẬN DỮ LIỆU:
- method: POST
- set_id

SỬ DỤNG:
- config/auth.php
- config/database.php
- user_admin/functions.php

LUỒNG:
pending_sets.php
        ↓
Admin bấm "Duyệt"
        ↓ POST set_id
process_approve_set.php
        ↓
requireAdmin()
        ↓
approveSet()
        ↓
flashcard_sets.status = approved
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


// 4. Chỉ xử lý request POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /*
    5. Lấy ID bộ Flashcard từ form.

    intval() chuyển dữ liệu thành số nguyên.
    */
    $setId = intval($_POST["set_id"] ?? 0);


    // ID phải lớn hơn 0.
    if ($setId <= 0) {
        die("ID bộ Flashcard không hợp lệ.");
    }


    // 6. Gọi hàm duyệt bộ Flashcard.
    $result = approveSet(
        $conn,
        $setId
    );


    // 7. Kiểm tra kết quả.
    if (!$result) {
        die("Không thể duyệt bộ Flashcard.");
    }


    /*
    8. Duyệt thành công.

    Quay về danh sách chờ duyệt.
    Bộ vừa duyệt sẽ không còn xuất hiện ở đây
    vì status đã đổi thành approved.
    */
    header(
        "Location: /PRJ_FLASHCARD/user_admin/admin/pending_sets.php"
    );

    exit();
}