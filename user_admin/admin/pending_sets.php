<?php

/*
==================================================
MODULE: NGƯỜI DÙNG & ADMIN
OWNER: Trần Đăng Khoa

FILE: admin/pending_sets.php

MỤC ĐÍCH:
- Hiển thị các bộ Flashcard public đang chờ duyệt
- Cho Admin xem thông tin bộ thẻ
- Sau này có nút Duyệt / Từ chối

QUYỀN:
- Guest : KHÔNG
- User  : KHÔNG
- Admin : ĐƯỢC

SỬ DỤNG:
- config/auth.php
- config/database.php
- user_admin/functions.php

LUỒNG:
public + pending
        ↓
pending_sets.php
        ↓
Admin xem
        ↓
Duyệt / Từ chối
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


// 4. Lấy danh sách bộ đang chờ duyệt.
$pendingSets = getPendingSets($conn);


// 5. Hiển thị giao diện.
include __DIR__ . "/../../includes/header.php";

?>

<h1>Bộ Flashcard chờ duyệt</h1>


<?php if (mysqli_num_rows($pendingSets) > 0): ?>

    <?php while ($set = mysqli_fetch_assoc($pendingSets)): ?>

        <div class="flashcard-set">

            <h2>
                <?php echo htmlspecialchars($set["title"]); ?>
            </h2>

            <p>
                Người tạo:
                <strong>
                    <?php echo htmlspecialchars($set["owner_name"]); ?>
                </strong>
            </p>

            <p>
                Chủ đề:
                <?php echo htmlspecialchars($set["subject"]); ?>
            </p>

            <p>
                Mô tả:
                <?php echo htmlspecialchars($set["description"]); ?>
            </p>

            <p>
                Trạng thái:
                <strong>
                    <?php echo htmlspecialchars($set["status"]); ?>
                </strong>
            </p>
            
            <div>

                    <!--
                    Duyệt bộ Flashcard.

                    Gửi ID của bộ thẻ sang:
                    process_approve_set.php

                    File xử lý sẽ đổi:
                    status = pending
                    thành:
                    status = approved
                    -->
                    <form
                        action="process_approve_set.php"
                        method="POST"
                        style="display: inline;"
                    >

                        <input
                            type="hidden"
                            name="set_id"
                            value="<?php echo $set["id"]; ?>"
                        >

                        <button type="submit">
                            Duyệt
                        </button>

                    </form>


                    <!--
                    Từ chối bộ Flashcard.

                    Gửi ID của bộ thẻ sang:
                    process_reject_set.php

                    File xử lý sẽ đổi:
                    status = pending
                    thành:
                    status = rejected
                    -->
                    <form
                        action="process_reject_set.php"
                        method="POST"
                        style="display: inline;"
                    >

                        <input
                            type="hidden"
                            name="set_id"
                            value="<?php echo $set["id"]; ?>"
                        >

                        <button type="submit">
                            Từ chối
                        </button>

                    </form>

                </div>
            
        </div>
        

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>Không có bộ Flashcard nào đang chờ duyệt.</p>

<?php endif; ?>


<?php

include __DIR__ . "/../../includes/footer.php";

?>