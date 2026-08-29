# README_OWNER — cards/

**Thành viên:** Lê Mai Thiện Độ
**MSSV:** 054206007557
**Module:** Quản lý & học Flashcard
**Folder:** `cards/` (+ `assets/js/study.js`)

## Phạm vi

CRUD câu hỏi–đáp án (`cards`) thuộc một `flashcard_sets` (qua `set_id`),
và giao diện học (Study) từng Card. Không tạo/sửa/xóa bộ Flashcard —
việc đó thuộc `sets/`. Không tự ghi `study_history` — việc đó thuộc
`progress/`.

## Đã có (từ trước)

- `getCardsBySet()`, `getCardById()`, `createCard()`, `updateCard()`,
  `deleteCard()` trong `cards/functions.php`.
- `index.php` — danh sách Card theo `set_id`, kiểm tra quyền
  Public+Approved / Owner / Admin.
- `create.php`, `edit.php` + `process_create.php`, `process_update.php`,
  `process_delete.php` — CRUD Card, có kiểm tra quyền owner.
- `study.php` + `study.js` — học từng Card, xem đáp án, đánh dấu
  Biết/Chưa biết, đếm total/correct/wrong/percent, gửi kết quả sang
  Progress bằng `fetch()`.

## Đã bổ sung trong bản cập nhật này

- **UI Study đẹp hơn + flip card**: bấm vào thẻ (hoặc nút "Xem đáp án")
  sẽ lật thẻ từ mặt Câu hỏi sang mặt Đáp án bằng CSS 3D transform
  (`.flip-card`, `.flip-card-inner`, class `is-flipped` do `study.js`
  gắn/gỡ).
- **Progress indicator**: có cả thanh tiến độ trực quan
  (`#study-progress-fill`) và chữ "Câu x / y" (`#study-progress`).
- **Test 0/1/nhiều Card**:
  - 0 Card: `study.php` không còn `die()` trắng trang; hiển thị màn
    hình `.study-empty` có link "Thêm Flashcard ngay" (nếu là owner)
    và "Quay lại danh sách Flashcard".
  - 1 Card: `currentIndex` chạy 0 → 1, `nextCard()` gọi thẳng
    `finishStudy()`, không lỗi.
  - Nhiều Card: luồng `showCard()` → `revealAnswer()` →
    `correctBtn`/`wrongBtn` → `nextCard()` lặp đúng đến hết mảng
    `studyCards`.
- **Chống double-click**: `study.js` dùng cờ `isProcessingAnswer` và
  disable ngay `correctBtn`/`wrongBtn` khi vừa bấm, mở khóa lại ở
  `showCard()` cho Card kế tiếp. `showAnswerBtn` cũng bị ẩn/disable
  sau khi đã lật để tránh gọi `revealAnswer()` lặp.
- CSS mới cho khu vực Study được thêm vào cuối
  `assets/css/style.css` (chỉ thêm class mới, không sửa rule cũ).

## Cần làm tiếp (còn lại, không thuộc phạm vi bản cập nhật này)

- Polish thêm UI tổng thể nếu nhóm thống nhất đổi theme/màu sắc chung.
- Test thủ công trên trình duyệt thật với dữ liệu MySQL thật (bản
  cập nhật này mới kiểm tra cú pháp PHP/JS/CSS do môi trường không
  có sẵn PHP + XAMPP để chạy trực tiếp).

## Không làm (đúng ranh giới module)

- Không CRUD `flashcard_sets` (thuộc `sets/`).
- Không tự `INSERT`/`UPDATE` bảng `study_history` (thuộc `progress/`).
- Không đổi tên Session (`user_id`, `user_name`, `role`).
- Không đổi hàm trong `config/auth.php`.
