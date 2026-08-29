# 06 — GIT WORKFLOW CHO NHÓM MỚI

## Branch đề xuất

```text
main
├── auth-huy
├── sets-quochuy
├── cards-thiendo
├── progress-dat
└── user-admin-khoa
```

## Luật đơn giản

1. `main` chỉ chứa bản đã ghép/test ổn.
2. Mỗi người code branch của mình.
3. Trước khi ghép, ghi rõ file đã sửa.
4. Không sửa file của người khác nếu chưa báo.
5. File dùng chung (`database.sql`, `config/`, `shared/`) phải báo nhóm trước.

## Commit gợi ý

```text
feat(auth): add login form
feat(sets): create flashcard set
fix(cards): validate empty answer
feat(admin): approve public set
```
