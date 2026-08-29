# MODULE SETS – QUẢN LÝ BỘ FLASHCARD

**Người phụ trách:** Nguyễn Quốc Huy  
**Vai trò:** Nhóm trưởng  
**Module:** `sets/`

## 1. Mục đích
Module `sets/` quản lý các bộ Flashcard:
- Xem bộ công khai
- Tìm kiếm, lọc
- Tạo, xem, sửa, xóa bộ thẻ
- Kiểm tra quyền sở hữu
- Quản lý Public/Private và Pending/Approved/Rejected

## 2. Các file
```text
sets/
├── index.php
├── create.php
├── detail.php
├── edit.php
├── functions.php
├── process_create.php
├── process_update.php
├── process_delete.php
└── README_OWNER.md
```

## 3. Quy tắc
- Guest được xem Public + Approved.
- User phải đăng nhập để tạo.
- User chỉ sửa/xóa bộ của mình.
- `user_id` lấy từ `$_SESSION["user_id"]`.
- Không nhận `user_id` từ form.
- Public mới tạo → `pending`.
- Private mới tạo → `approved`.
- Public sau khi sửa → `pending`.
- Dùng Prepared Statement.
- Dùng `htmlspecialchars()` khi hiển thị dữ liệu.

## 4. Database
Bảng chính: `flashcard_sets`

Các cột:
`id`, `user_id`, `title`, `subject`, `description`, `visibility`, `status`, `created_at`, `updated_at`

## 5. Luồng
Guest:
```text
Sets → Search/Filter → Detail → Cards → Study
```

User:
```text
Login → Sets → Create/Edit/Delete
```

Public:
```text
Create → Pending → Admin duyệt → Approved
```

## 6. Test
- Guest xem Public Approved.
- Guest không Create/Edit/Delete.
- User tạo Private và Public.
- User không sửa/xóa Set của người khác.
- Public Pending không xuất hiện với Guest.
- Sau Admin Approve, Public Set xuất hiện.

## 7. Không tự ý sửa
Không tự ý thay đổi:
```text
config/database.php
config/auth.php
includes/header.php
includes/footer.php
database.sql
```

Nếu cần thay đổi phải thống nhất với nhóm.
