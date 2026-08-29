<?php

/*
==================================================
FILE: index.php

MỤC ĐÍCH:
- Trang chủ công khai của Flashcard IT
- KHÔNG bắt buộc đăng nhập
- Guest có thể:
    + xem giới thiệu
    + xem Flashcard public
    + đi tới trang tìm kiếm bộ thẻ

QUYỀN:
- Guest : ĐƯỢC
- User  : ĐƯỢC
- Admin : ĐƯỢC
==================================================
*/


// 1. Database.
require_once __DIR__ . "/config/database.php";

/** @var mysqli $conn */


// 2. Hàm Sets để lấy bộ thẻ công khai.
require_once __DIR__ . "/sets/functions.php";


// 3. Chỉ lấy:
// visibility = public
// status = approved
$publicSets = getPublicSets($conn);


// 4. Header chung.
include __DIR__ . "/includes/header.php";

?>

<section>

    <h1>Flashcard IT</h1>

    <p>
        Học tập và ghi nhớ kiến thức
        thông qua các bộ Flashcard.
    </p>


    <!--
    Guest có thể khám phá Flashcard
    mà chưa cần đăng nhập.
    -->
    <p>
        <a href="/PRJ_FLASHCARD/sets/index.php">
            Khám phá bộ Flashcard
        </a>
    </p>

</section>


<section>

    <h2>Bộ Flashcard công khai</h2>


    <?php if (mysqli_num_rows($publicSets) > 0): ?>

        <?php while ($set = mysqli_fetch_assoc($publicSets)): ?>

            <div class="flashcard-set">

                <h3>
                    <?php
                    echo htmlspecialchars(
                        $set["title"]
                    );
                    ?>
                </h3>


                <p>
                    Chủ đề:
                    <?php
                    echo htmlspecialchars(
                        $set["subject"]
                    );
                    ?>
                </p>


                <p>
                    <?php
                    echo htmlspecialchars(
                        $set["description"]
                    );
                    ?>
                </p>


                <a
                    href="/PRJ_FLASHCARD/sets/detail.php?id=<?php echo $set["id"]; ?>"
                >
                    Xem bộ thẻ
                </a>

            </div>

            <hr>

        <?php endwhile; ?>


    <?php else: ?>

        <p>
            Hiện chưa có bộ Flashcard công khai.
        </p>

    <?php endif; ?>

</section>


<?php

include __DIR__ . "/includes/footer.php";

?>