<?php

/*
==================================================
MODULE: KẾT QUẢ & TIẾN ĐỘ

FILE: history.php

MỤC ĐÍCH:
- Hiển thị lịch sử học của user hiện tại

QUYỀN:
- Guest : KHÔNG
- User  : ĐƯỢC xem lịch sử của chính mình

USER_ID:
- lấy từ Session
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


// 4. Lấy lịch sử của user hiện tại.
$userId = $_SESSION["user_id"];

$history = getStudyHistory(
    $conn,
    $userId
);


// 5. Giao diện chung.
include __DIR__ . "/../includes/header.php";

?>

<h1>Lịch sử học</h1>


<p>
    <a href="statistics.php">
        Xem thống kê
    </a>
</p>


<?php if (mysqli_num_rows($history) > 0): ?>

    <table border="1" cellpadding="10">

        <thead>

            <tr>
                <th>Bộ Flashcard</th>
                <th>Tổng câu</th>
                <th>Biết</th>
                <th>Chưa biết</th>
                <th>Phần trăm</th>
                <th>Thời gian</th>
            </tr>

        </thead>


        <tbody>

            <?php while ($row = mysqli_fetch_assoc($history)): ?>

                <tr>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row["set_title"]
                        );
                        ?>
                    </td>

                    <td>
                        <?php echo $row["total"]; ?>
                    </td>

                    <td>
                        <?php echo $row["correct"]; ?>
                    </td>

                    <td>
                        <?php echo $row["wrong"]; ?>
                    </td>

                    <td>
                        <?php echo $row["percent"]; ?>%
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            formatDateTime($row["created_at"])
                        );
                        ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </tbody>

    </table>


<?php else: ?>

    <p>
        Bạn chưa có lịch sử học nào.
    </p>

<?php endif; ?>


<?php

include __DIR__ . "/../includes/footer.php";

?>