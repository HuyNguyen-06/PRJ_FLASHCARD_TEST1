# 02 — DATABASE CONTRACT

Database: `flashcard_it`

## 1. users

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| id | INT PK AUTO_INCREMENT | ID user |
| full_name | VARCHAR(100) | Họ tên |
| email | VARCHAR(150) UNIQUE | Email login |
| password | VARCHAR(255) | Mật khẩu đã hash |
| role | ENUM('user','admin') | Quyền |
| interests | VARCHAR(255) NULL | Chủ đề quan tâm |
| theme | ENUM('light','dark') | Giao diện |
| notif_email | TINYINT(1) | Bật/tắt email |
| created_at | DATETIME | Ngày tạo |

## 2. flashcard_sets

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| id | INT PK AUTO_INCREMENT | ID bộ thẻ |
| user_id | INT FK -> users.id | Chủ sở hữu |
| title | VARCHAR(150) | Tên bộ thẻ |
| description | TEXT NULL | Mô tả |
| subject | VARCHAR(100) | Môn/chủ đề |
| visibility | ENUM('private','public') | Quyền xem |
| status | ENUM('pending','approved','rejected') | Trạng thái duyệt |
| created_at | DATETIME | Ngày tạo |
| updated_at | DATETIME | Ngày cập nhật |

Quy ước:
- Bộ `private`: `status = approved`.
- Bộ `public` mới tạo: `status = pending`.
- Admin duyệt: `approved`.
- Admin từ chối: `rejected`.

## 3. cards

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| id | INT PK AUTO_INCREMENT | ID thẻ |
| set_id | INT FK -> flashcard_sets.id | Bộ thẻ chứa card |
| question | TEXT | Câu hỏi |
| answer | TEXT | Đáp án |
| created_at | DATETIME | Ngày tạo |
| updated_at | DATETIME | Ngày sửa |

## 4. study_history

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| id | INT PK AUTO_INCREMENT | ID phiên học |
| user_id | INT FK -> users.id | Người học |
| set_id | INT FK -> flashcard_sets.id | Bộ thẻ đã học |
| total | INT | Tổng thẻ |
| correct | INT | Đã thuộc |
| wrong | INT | Chưa thuộc |
| percent | INT | % hoàn thành |
| studied_at | DATETIME | Thời gian học |

## Luật thay đổi database

Không thành viên nào tự thêm/đổi/xóa cột trên `main` mà chưa báo nhóm.
