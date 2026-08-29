# 01 — TEAM MAP

| Thành viên | MSSV | Module | Folder | Phạm vi |
|---|---|---|---|---|
| Nguyễn Ngọc Huy | 072206001705 | Xác thực người dùng | `auth/` | Giới thiệu, đăng ký, đăng nhập, đăng xuất, quên mật khẩu, session |
| Nguyễn Quốc Huy | 52206016101 | Quản lý bộ Flashcard | `sets/` | Tạo, xem, sửa, xóa, tìm kiếm, lọc, quyền truy cập bộ thẻ |
| Lê Mai Thiện Độ | 054206007557 | Quản lý & học Flashcard | `cards/` | CRUD câu hỏi–đáp án trong bộ, giao diện học |
| Phạm Tiến Đạt | 026205000550 | Kết quả & tiến độ | `progress/` | Kết quả phiên học, lịch sử, %, thống kê, chuỗi học |
| Trần Đăng Khoa | 92206011743 | Người dùng & Admin | `user_admin/` | Hồ sơ, đổi mật khẩu, cài đặt, quản lý user, duyệt bộ thẻ, dashboard Admin |

## Ranh giới module

### Auth
Ghi vào `users`, tạo/hủy session. Không quản lý flashcard.

### Sets
CRUD `flashcard_sets`. Không CRUD từng card.

### Cards
CRUD `cards` thuộc một `set_id`; thực hiện phiên học. Không tự sở hữu thống kê lịch sử.

### Progress
Nhận kết quả phiên học và ghi `study_history`; tính lịch sử/thống kê.

### User & Admin
Cập nhật hồ sơ/cài đặt user; quản lý user; đọc/duyệt `flashcard_sets`; đọc dữ liệu tổng hợp để làm dashboard Admin.
