<?php
require_once "../config/auth.php";
requireLogin();

require_once "../config/database.php";
require_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Phương thức không hợp lệ.");
}

$userId = $_SESSION["user_id"];
$setId = intval($_POST["set_id"] ?? 0);
$title = trim($_POST["title"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$description = trim($_POST["description"] ?? "");
$visibility = $_POST["visibility"] ?? "";

$set = getSetById($conn, $setId);

if (!$set) {
    die("Không tìm thấy bộ Flashcard.");
}

if ($set["user_id"] != $userId) {
    die("Bạn không có quyền sửa bộ Flashcard này.");
}

if ($title == "") {
    die("Tên bộ Flashcard không được để trống.");
}

if ($visibility != "private" && $visibility != "public") {
    die("Quyền riêng tư không hợp lệ.");
}

$status = ($visibility == "public") ? "pending" : "approved";

if (!updateFlashcardSet($conn, $setId, $userId, $title, $subject, $description, $visibility, $status)) {
    die("Cập nhật thất bại.");
}

header("Location: detail.php?id=" . $setId);
exit();
?>
