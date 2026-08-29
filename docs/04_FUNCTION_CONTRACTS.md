# 04 — FUNCTION CONTRACTS

Đây là tên hàm khuyến nghị để các module gọi/đọc dễ hiểu. Nếu muốn đổi tên, phải thống nhất trước.

## Nguyễn Ngọc Huy — auth/

```php
getUserByEmail($conn, $email)
registerUser($conn, $fullName, $email, $password)
loginUser($conn, $email, $password)
logoutUser()
```

Session output sau login:

```php
$_SESSION['user_id']
$_SESSION['user_name']
$_SESSION['role']
```

## Nguyễn Quốc Huy — sets/

```php
getUserSets($conn, $userId)
getSetById($conn, $setId)
createFlashcardSet($conn, $userId, $title, $description, $subject, $visibility)
updateFlashcardSet($conn, $setId, $userId, $title, $description, $subject, $visibility)
deleteFlashcardSet($conn, $setId, $userId)
searchFlashcardSets($conn, $userId, $keyword, $subject)
```

## Lê Mai Thiện Độ — cards/

```php
getCardsBySet($conn, $setId)
getCardById($conn, $cardId)
createCard($conn, $setId, $question, $answer)
updateCard($conn, $cardId, $question, $answer)
deleteCard($conn, $cardId)
```

Kết thúc học gửi sang Progress:

```text
POST progress/save_result.php
set_id
total
correct
wrong
```

## Phạm Tiến Đạt — progress/

```php
saveStudyResult($conn, $userId, $setId, $total, $correct, $wrong)
getStudyHistory($conn, $userId)
calculateAveragePercent($history)
calculateStudyStreak($history)
getProgressStatistics($conn, $userId)
```

## Trần Đăng Khoa — user_admin/

```php
getUserProfile($conn, $userId)
updateUserProfile($conn, $userId, $fullName, $interests)
changeUserPassword($conn, $userId, $currentPassword, $newPassword)
updateUserSettings($conn, $userId, $theme, $notifEmail)
getAllUsers($conn)
deleteUser($conn, $userId)
getPendingSets($conn)
approveSet($conn, $setId)
rejectSet($conn, $setId)
getAdminStatistics($conn)
```
# ACCESS CONTROL CONTRACT

## 1. Nguyên tắc chung

Website KHÔNG bắt buộc đăng nhập ngay khi người dùng truy cập.

Guest vẫn được phép xem các nội dung công khai.

Chỉ khi người dùng truy cập chức năng cần xác định tài khoản
thì mới yêu cầu đăng nhập.

---

## 2. Trang PUBLIC

Các trang sau không gọi requireLogin():

- index.php
- auth/login.php
- auth/register.php
- auth/forgot_password.php
- sets/index.php
- sets/detail.php nếu bộ thẻ là public

Guest có thể:
- xem trang chủ
- xem danh sách bộ Flashcard public
- tìm kiếm bộ Flashcard public
- xem chi tiết bộ Flashcard public

---

## 3. Trang yêu cầu LOGIN

Các chức năng cá nhân phải gọi:

requireLogin();

Ví dụ:

- dashboard.php
- sets/create.php
- sets/edit.php
- sets/process_create.php
- sets/process_update.php
- sets/process_delete.php
- progress/history.php
- progress/statistics.php
- user_admin/profile.php
- user_admin/change_password.php
- user_admin/settings.php

---

## 4. Trang yêu cầu ADMIN

Các trang trong:

user_admin/admin/

phải gọi:

requireAdmin();

Ví dụ:

- user_admin/admin/dashboard.php
- user_admin/admin/users.php
- user_admin/admin/user_detail.php
- user_admin/admin/pending_sets.php
- các file approve/reject/delete của Admin

---

## 5. Session chung

Toàn bộ project sử dụng đúng:

$_SESSION['user_id']
$_SESSION['user_name']
$_SESSION['role']

Không tự tạo tên Session khác.

---

## 6. Shared Auth Functions

Các hàm bảo vệ dùng chung nằm tại:

config/auth.php

Bao gồm:

- isLoggedIn()
- isAdmin()
- requireLogin()
- requireAdmin()

KHÔNG khai báo lại các hàm này trong module auth/functions.php.

auth/functions.php chỉ chứa logic nghiệp vụ như:

- getUserByEmail()
- registerUser()
- loginUser()