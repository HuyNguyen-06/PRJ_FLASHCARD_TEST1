<?php

session_start(); //Session là vùng dữ liệu dành cho người đang sd Web

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /PRJ_FLASHCARD/auth/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: /PRJ_FLASHCARD/auth/login.php");
        exit();
    }

    if (!isAdmin()) {
        header("Location: /PRJ_FLASHCARD/dashboard.php");
        exit();
    }
}