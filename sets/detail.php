<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "functions.php";

$setId = intval($_GET["id"] ?? 0);
$set = getSetById($conn, $setId);

if (!$set) {
    die("Không tìm thấy bộ Flashcard.");
}

$isOwner = isLoggedIn() && $set["user_id"] == $_SESSION["user_id"];
$isAdmin = isLoggedIn() && ($_SESSION["role"] ?? "") == "admin";
$isPublicApproved = $set["visibility"] == "public" && $set["status"] == "approved";

if (!$isPublicApproved && !$isOwner && !$isAdmin) {
    die("Bạn không có quyền xem bộ Flashcard này.");
}

require_once "../includes/header.php";
?>

<h1><?php echo htmlspecialchars($set["title"]); ?></h1>

<p>Môn học: <?php echo htmlspecialchars($set["subject"]); ?></p>
<p>Mô tả: <?php echo htmlspecialchars($set["description"]); ?></p>
<p>Người tạo: <?php echo htmlspecialchars($set["owner_name"]); ?></p>
<p>Quyền: <?php echo htmlspecialchars($set["visibility"]); ?></p>
<p>Trạng thái: <?php echo htmlspecialchars($set["status"]); ?></p>

<?php if ($isOwner || $isAdmin): ?>

    <?php if ($isOwner): ?>
        <a href="edit.php?id=<?php echo $set["id"]; ?>">Sửa</a>
    <?php endif; ?>

    <?php if ($isOwner): ?>
        <form action="process_delete.php" method="POST"
              onsubmit="return confirm('Bạn có chắc muốn xóa bộ này?');">
            <input type="hidden" name="set_id" value="<?php echo $set["id"]; ?>">
            <button type="submit">Xóa</button>
        </form>
    <?php endif; ?>

<?php endif; ?>

<p><a href="index.php">Quay lại</a></p>

<?php require_once "../includes/footer.php"; ?>
