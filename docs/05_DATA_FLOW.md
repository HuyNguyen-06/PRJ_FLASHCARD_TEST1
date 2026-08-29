# 05 — DATA FLOW GIỮA CÁC THÀNH VIÊN

## Đăng ký/login -> các module khác

```text
Auth
  -> users
  -> $_SESSION['user_id']
  -> Dashboard / Sets / Cards / Progress / User Admin
```

## Tạo bộ -> Admin duyệt

```text
Sets tạo public set
  -> flashcard_sets.status = pending
  -> User/Admin đọc pending
  -> approve/reject
```

## Bộ thẻ -> Cards

```text
Sets tạo set_id
  -> Cards nhận set_id
  -> CRUD cards WHERE set_id = ?
```

## Học -> Progress

```text
Cards/Study kết thúc
  -> POST set_id,total,correct,wrong
  -> Progress tính percent
  -> INSERT study_history
```

## Progress -> Admin Dashboard

```text
study_history
  -> Admin chỉ SELECT/tổng hợp
  -> không sửa công thức nghiệp vụ của Progress
```
