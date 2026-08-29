<?php
require_once "../config/auth.php";
requireLogin();

require_once "../config/database.php";
require_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Phương thức không hợp lệ.");
}

$setId = intval($_POST["set_id"] ?? 0);
$userId = $_SESSION["user_id"];

$set = getSetById($conn, $setId);

if (!$set) {
    die("Không tìm thấy bộ Flashcard.");
}

if ($set["user_id"] != $userId) {
    die("Bạn không có quyền xóa bộ Flashcard này.");
}

if (!deleteFlashcardSet($conn, $setId, $userId)) {
    die("Xóa bộ Flashcard thất bại.");
}

header("Location: index.php");
exit();
?>
