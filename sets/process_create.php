<?php
require_once "../config/auth.php";
requireLogin();

require_once "../config/database.php";
require_once "functions.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Phương thức không hợp lệ.");
}

$userId = $_SESSION["user_id"];
$title = trim($_POST["title"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$description = trim($_POST["description"] ?? "");
$visibility = $_POST["visibility"] ?? "";

if ($title == "") {
    die("Vui lòng nhập tên bộ Flashcard.");
}

if ($visibility != "private" && $visibility != "public") {
    die("Quyền riêng tư không hợp lệ.");
}

$status = ($visibility == "public") ? "pending" : "approved";

if (!createFlashcardSet($conn, $userId, $title, $subject, $description, $visibility, $status)) {
    die("Tạo bộ Flashcard thất bại.");
}

header("Location: index.php");
exit();
?>
