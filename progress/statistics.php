<?php

/*
==================================================
MODULE: KẾT QUẢ & TIẾN ĐỘ

FILE: statistics.php

MỤC ĐÍCH:
- Hiển thị thống kê học tập cá nhân
- Hiển thị streak

QUYỀN:
- Phải đăng nhập
==================================================
*/


// 1. Login.
require_once __DIR__ . "/../config/auth.php";

requireLogin();


// 2. Database.
require_once __DIR__ . "/../config/database.php";

/** @var mysqli $conn */


// 3. Progress functions.
require_once __DIR__ . "/functions.php";


$userId = $_SESSION["user_id"];


// 4. Lấy thống kê.
$statistics = getProgressStatistics(
    $conn,
    $userId
);


// 5. Tính streak.
$streak = calculateStudyStreak(
    $conn,
    $userId
);


// 6. Header.
include __DIR__ . "/../includes/header.php";

?>

<h1>Thống kê học tập</h1>


<div>

    <h2>
        <?php
        echo $statistics["total_sessions"];
        ?>
    </h2>

    <p>Phiên học</p>

</div>


<div>

    <h2>
        <?php
        echo $statistics["total_questions"];
        ?>
    </h2>

    <p>Tổng câu đã học</p>

</div>


<div>

    <h2>
        <?php
        echo $statistics["total_correct"];
        ?>
    </h2>

    <p>Câu đã biết</p>

</div>


<div>

    <h2>
        <?php
        echo $statistics["average_percent"];
        ?>%
    </h2>

    <p>Điểm trung bình</p>

</div>


<div>

    <h2>
        <?php echo $streak; ?>
    </h2>

    <p>Ngày học liên tiếp 🔥</p>

</div>


<p>
    <a href="history.php">
        Xem lịch sử học
    </a>
</p>


<?php

include __DIR__ . "/../includes/footer.php";

?>