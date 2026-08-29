<?php

/*
==================================================
MODULE: KẾT QUẢ & TIẾN ĐỘ
OWNER: Phạm Tiến Đạt

FILE: functions.php

MỤC ĐÍCH:
- Chứa các hàm xử lý kết quả học
- Lưu lịch sử học
- Sau này:
    + lấy lịch sử
    + thống kê
    + tính trung bình
    + tính streak

DATABASE:
- study_history

LƯU Ý:
- Module Cards chỉ tạo kết quả phiên học
- Module Progress chịu trách nhiệm LƯU kết quả
==================================================
*/


/*
--------------------------------------------------
HÀM: saveStudyResult()

Mục đích:
- Lưu kết quả một phiên học vào study_history

Input:
- $conn
- $userId
- $setId
- $total
- $correct
- $wrong
- $percent

Output:
- true  : lưu thành công
- false : lưu thất bại
--------------------------------------------------
*/
function saveStudyResult(
    $conn,
    $userId,
    $setId,
    $total,
    $correct,
    $wrong,
    $percent
) {
    $sql = "
        INSERT INTO study_history
        (
            user_id,
            set_id,
            total,
            correct,
            wrong,
            percent
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iiiiii",
        $userId,
        $setId,
        $total,
        $correct,
        $wrong,
        $percent
    );

    return mysqli_stmt_execute($stmt);
}
/*
--------------------------------------------------
HÀM: getStudyHistory()

Mục đích:
- Lấy lịch sử học của một user
- Lấy thêm tên bộ Flashcard

Input:
- $conn
- $userId

Output:
- Danh sách các phiên học
--------------------------------------------------
*/
function getStudyHistory($conn, $userId)
{
    $sql = "
        SELECT
            study_history.*,
            flashcard_sets.title AS set_title

        FROM study_history

        JOIN flashcard_sets
        ON study_history.set_id = flashcard_sets.id

        WHERE study_history.user_id = ?

        ORDER BY study_history.created_at DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}
/*
--------------------------------------------------
HÀM: getProgressStatistics()

Mục đích:
- Tổng hợp toàn bộ lịch sử học của user

Thống kê:
- số phiên học
- tổng số câu đã học
- tổng số câu biết
- phần trăm trung bình
--------------------------------------------------
*/
function getProgressStatistics($conn, $userId)
{
    $sql = "
        SELECT
            COUNT(*) AS total_sessions,

            COALESCE(
                SUM(total),
                0
            ) AS total_questions,

            COALESCE(
                SUM(correct),
                0
            ) AS total_correct,

            COALESCE(
                ROUND(AVG(percent)),
                0
            ) AS average_percent

        FROM study_history

        WHERE user_id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}
/*
--------------------------------------------------
HÀM: calculateStudyStreak()

Mục đích:
- Tính số ngày học liên tiếp

Ví dụ:
22/08 có học
23/08 có học
24/08 có học

=> streak = 3

Nếu bỏ một ngày:
22/08
24/08

=> chuỗi bị ngắt
--------------------------------------------------
*/
function calculateStudyStreak($conn, $userId)
{
    $sql = "
        SELECT DISTINCT
            DATE(created_at) AS study_date

        FROM study_history

        WHERE user_id = ?

        ORDER BY study_date DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $userId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);


    // Chứa các ngày user đã học.
    $dates = [];

    while ($row = mysqli_fetch_assoc($result)) {

        $dates[] = $row["study_date"];
    }


    // Chưa học lần nào.
    if (count($dates) == 0) {
        return 0;
    }


    /*
    Ngày học gần nhất phải là:
    - hôm nay
    hoặc
    - hôm qua

    Nếu lâu hơn thì streak = 0.
    */
    $today = strtotime(
        date("Y-m-d")
    );

    $latestStudyDate = strtotime(
        $dates[0]
    );

    $daysFromToday =
        ($today - $latestStudyDate)
        / 86400;


    if ($daysFromToday > 1) {
        return 0;
    }


    $streak = 1;

    $previousDate =
        $latestStudyDate;


    /*
    Duyệt các ngày tiếp theo.

    Nếu hai ngày cách nhau đúng 1 ngày:
    -> streak tiếp tục.

    Nếu cách > 1:
    -> chuỗi bị ngắt.
    */
    for (
        $i = 1;
        $i < count($dates);
        $i++
    ) {

        $currentDate =
            strtotime($dates[$i]);


        $difference =
            ($previousDate - $currentDate)
            / 86400;


        if ($difference == 1) {

            $streak++;

            $previousDate =
                $currentDate;

        } else {

            break;
        }
    }


    return $streak;
}
/*
--------------------------------------------------
HÀM: formatDateTime()

Mục đích:
- Chuẩn hóa hiển thị ngày giờ MySQL (created_at)
  sang định dạng dd/mm/yyyy HH:ii cho người Việt

Input:
- $datetime : chuỗi datetime lấy từ database
              (ví dụ: 2026-08-27 14:05:00)

Output:
- Chuỗi đã format (ví dụ: 27/08/2026 14:05)
- Chuỗi rỗng nếu input rỗng
- Trả lại nguyên input nếu không parse được
--------------------------------------------------
*/
function formatDateTime($datetime)
{
    if ($datetime == null || $datetime == "") {
        return "";
    }

    $timestamp = strtotime($datetime);

    // Không parse được thì trả về nguyên gốc,
    // tránh làm mất dữ liệu hiển thị.
    if ($timestamp === false) {
        return $datetime;
    }

    return date("d/m/Y H:i", $timestamp);
}
/*
--------------------------------------------------
HÀM: getLatestResultBySet()

Mục đích:
- Lấy phiên học GẦN NHẤT của một user
  trong một bộ Flashcard cụ thể
- Dùng cho result.php để hiển thị kết quả
  vừa hoàn thành, KHÔNG lấy total/correct/wrong
  từ query string (tránh người dùng tự sửa URL)

Input:
- $conn   : kết nối database
- $userId : lấy từ Session
- $setId  : ID bộ Flashcard vừa học

Output:
- Mảng thông tin phiên học gần nhất (kèm set_title)
- null nếu user chưa học bộ này lần nào
--------------------------------------------------
*/
function getLatestResultBySet($conn, $userId, $setId)
{
    $sql = "
        SELECT
            study_history.*,
            flashcard_sets.title AS set_title

        FROM study_history

        JOIN flashcard_sets
        ON study_history.set_id = flashcard_sets.id

        WHERE study_history.user_id = ?
        AND study_history.set_id = ?

        ORDER BY study_history.created_at DESC

        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $userId,
        $setId
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return $row;
    }

    return null;
}
/*
--------------------------------------------------
HÀM: hasDuplicateRecentResult()

Mục đích:
- Chống lưu trùng kết quả khi:
    + người dùng refresh trang sau khi học xong
    + fetch() bị gọi lặp (double submit)
    + mất mạng và JS tự động gửi lại request

Cách kiểm tra:
- Tìm một dòng study_history của CÙNG user + CÙNG set
  có total/correct/wrong giống hệt
  và được tạo trong vòng $windowSeconds giây gần đây

Input:
- $conn
- $userId
- $setId
- $total
- $correct
- $wrong
- $windowSeconds : khoảng thời gian coi là trùng (mặc định 5s)

Output:
- true  : đã có bản ghi trùng gần đây -> KHÔNG insert nữa
- false : chưa có -> được phép lưu bình thường
--------------------------------------------------
*/
function hasDuplicateRecentResult(
    $conn,
    $userId,
    $setId,
    $total,
    $correct,
    $wrong,
    $windowSeconds = 5
) {
    $sql = "
        SELECT id
        FROM study_history

        WHERE user_id = ?
        AND set_id = ?
        AND total = ?
        AND correct = ?
        AND wrong = ?
        AND created_at >= (NOW() - INTERVAL ? SECOND)

        ORDER BY created_at DESC

        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iiiiii",
        $userId,
        $setId,
        $total,
        $correct,
        $wrong,
        $windowSeconds
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    return $row !== null;
}