# PHẠM TIẾN ĐẠT — PROGRESS
MSSV: 026205000550

## Cập nhật ngày 27/08/2026

Đã hoàn thành 3 việc trong danh sách "Cần làm tiếp":

### 1. result.php hữu ích hơn
- Nhận `set_id` qua URL (`result.php?set_id=5`).
- KHÔNG lấy total/correct/wrong từ URL — luôn truy vấn lại
  bản ghi `study_history` gần nhất của đúng `user_id` (Session) +
  `set_id` đó bằng hàm mới `getLatestResultBySet()`.
- Hiển thị: tên bộ, phần trăm, tổng câu/đã biết/chưa biết, thời gian.
- Style riêng viết trong `<style>` scoped ngay trong file,
  KHÔNG đụng vào `assets/css/style.css` (file dùng chung).

**Cần phối hợp với module Cards (Lê Mai Thiện Độ):**
Sau khi `save_result.php` trả `success: true`, `study.js`/`study.php`
nên điều hướng sang `progress/result.php?set_id=<setId>` thay vì
ở nguyên trang học, để người dùng thấy kết quả rõ ràng.

### 2. Format ngày giờ
- Thêm hàm `formatDateTime($datetime)` trong `functions.php`,
  trả về định dạng `dd/mm/yyyy HH:ii`.
- Áp dụng tại `history.php` (cột "Thời gian") và `result.php`.

### 3. Chống refresh / double submit
- Thêm hàm `hasDuplicateRecentResult()`: kiểm tra nếu đã có
  bản ghi `study_history` GIỐNG HỆT (cùng user, cùng set, cùng
  total/correct/wrong) được tạo trong 5 giây gần nhất thì KHÔNG
  insert thêm — trả về `success: true, duplicate: true`.
- Áp dụng trong `save_result.php`, trước khi gọi `saveStudyResult()`.
- Cách này tự chứa trong module `progress/`, không cần sửa
  module khác (không cần token từ `cards/study.php`).

## Functions mới thêm vào functions.php
```php
formatDateTime($datetime)
getLatestResultBySet($conn, $userId, $setId)
hasDuplicateRecentResult($conn, $userId, $setId, $total, $correct, $wrong, $windowSeconds = 5)
```

## Chưa làm (còn lại trong "Cần làm tiếp")
- UI History/Statistics (mới chỉnh phần ngày giờ, chưa làm lại
  toàn bộ giao diện bảng).
- Test nhiều phiên học liên tục (cần dữ liệu thật, không phải
  việc sửa code).
- Test streak qua nhiều ngày (cần seed dữ liệu study_history
  ở nhiều ngày khác nhau để kiểm chứng `calculateStudyStreak()`).
