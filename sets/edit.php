<?php
require_once "../config/auth.php";
requireLogin();

require_once "../config/database.php";
require_once "functions.php";

$setId = intval($_GET["id"] ?? 0);
$set = getSetById($conn, $setId);

if (!$set) {
    die("Không tìm thấy bộ Flashcard.");
}

if ($set["user_id"] != $_SESSION["user_id"]) {
    die("Bạn không có quyền sửa bộ Flashcard này.");
}

require_once "../includes/header.php";
?>

<h1>Sửa bộ Flashcard</h1>

<form action="process_update.php" method="POST">
    <input type="hidden" name="set_id" value="<?php echo $set["id"]; ?>">

    <label>Tên bộ Flashcard</label><br>
    <input type="text" name="title"
           value="<?php echo htmlspecialchars($set["title"]); ?>" required><br><br>

    <label>Môn học / Chủ đề</label><br>
    <input type="text" name="subject"
           value="<?php echo htmlspecialchars($set["subject"]); ?>"><br><br>

    <label>Mô tả</label><br>
    <textarea name="description"><?php echo htmlspecialchars($set["description"]); ?></textarea><br><br>

    <label>Quyền riêng tư</label><br>
    <select name="visibility">
        <option value="private" <?php if ($set["visibility"] == "private") echo "selected"; ?>>
            Riêng tư
        </option>
        <option value="public" <?php if ($set["visibility"] == "public") echo "selected"; ?>>
            Công khai
        </option>
    </select><br><br>

    <button type="submit">Lưu thay đổi</button>
</form>

<p><a href="detail.php?id=<?php echo $set["id"]; ?>">Quay lại</a></p>

<?php require_once "../includes/footer.php"; ?>
