<?php
require_once "../config/auth.php";
requireLogin();

require_once "../includes/header.php";
?>

<h1>Tạo bộ Flashcard</h1>

<form action="process_create.php" method="POST">
    <label>Tên bộ Flashcard</label><br>
    <input type="text" name="title" required><br><br>

    <label>Môn học / Chủ đề</label><br>
    <input type="text" name="subject"><br><br>

    <label>Mô tả</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Quyền riêng tư</label><br>
    <select name="visibility">
        <option value="private">Riêng tư</option>
        <option value="public">Công khai</option>
    </select><br><br>

    <button type="submit">Tạo bộ thẻ</button>
</form>

<?php require_once "../includes/footer.php"; ?>
