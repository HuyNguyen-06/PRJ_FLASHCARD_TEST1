CREATE DATABASE flashcard_it;

USE flashcard_it;


-- =============================================
-- BẢNG 1: USERS
-- Dùng chung cho Auth + User/Admin
-- =============================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',

    interests VARCHAR(255),
    theme VARCHAR(20) DEFAULT 'light',
    notif_email TINYINT DEFAULT 0,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- =============================================
-- BẢNG 2: FLASHCARD SETS
-- Người phụ trách chính: Nguyễn Quốc Huy
-- =============================================

CREATE TABLE flashcard_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    title VARCHAR(150) NOT NULL,
    subject VARCHAR(100),
    description TEXT,

    visibility VARCHAR(20) DEFAULT 'private',
    status VARCHAR(20) DEFAULT 'approved',

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- =============================================
-- BẢNG 3: CARDS
-- Người phụ trách chính: Lê Mai Thiện Độ
-- =============================================

CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    set_id INT NOT NULL,

    question TEXT NOT NULL,
    answer TEXT NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- =============================================
-- BẢNG 4: STUDY HISTORY
-- Người phụ trách chính: Phạm Tiến Đạt
-- =============================================

CREATE TABLE study_history (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    set_id INT NOT NULL,

    total INT DEFAULT 0,
    correct INT DEFAULT 0,
    wrong INT DEFAULT 0,
    percent INT DEFAULT 0,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);