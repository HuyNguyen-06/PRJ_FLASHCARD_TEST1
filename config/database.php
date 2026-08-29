<?php

$host = "localhost"; //MySQL dang chay qua XAMPP nen dung localhost
$user = "root"; //tk mawc dinh cua XAMP la root
$password = "";
$database = "flashcard_it"; //database ma nhom thong nhat

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4"); // de MySQL xu ly tieng Viet tot hon