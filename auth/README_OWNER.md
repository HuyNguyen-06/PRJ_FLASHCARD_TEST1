# AUTH - Nguyễn Ngọc Huy

MSSV: 052206016101

## Phạm vi phụ trách

Module:
auth/

## Chức năng

- Register
- Login
- Logout
- Password Hash
- Password Verify
- Session
- User/Admin Role
- Forgot Password
- Register Validation
- Email Duplicate Validation
- Login Error Handling

## Session Contract

Không thay đổi Session contract của project.

Các Session được sử dụng:

- $_SESSION['user_id']
- $_SESSION['user_name']
- $_SESSION['role']

## Shared Authentication

Không khai báo lại:

- isLoggedIn()
- isAdmin()
- requireLogin()
- requireAdmin()

Các hàm này nằm trong:

config/auth.php

## Database

Sử dụng database chung:

flashcard_it

Bảng chính:

users

Bảng hỗ trợ Forgot Password:

password_resets

## Password Security

Password được lưu bằng:

password_hash()

Password login được kiểm tra bằng:

password_verify()

Reset token được lưu dưới dạng SHA-256 hash.

## Forgot Password

Flow:

Login
→ Quên mật khẩu
→ Nhập email
→ Tạo reset token
→ Token hết hạn sau 15 phút
→ Đặt password mới
→ Hash password
→ Xóa token
→ Login lại

## Không phụ trách

- Sets CRUD
- Cards CRUD
- Study
- Progress
- Profile
- Admin CRUD

## Lưu ý

Không thay đổi:

- Database contract ngoài phần password_resets đã được thống nhất
- Session contract
- Permission contract
- Naming contract
- Shared UI  