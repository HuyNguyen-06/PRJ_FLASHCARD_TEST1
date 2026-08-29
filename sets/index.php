<?php
require_once "../config/database.php";
require_once "../config/auth.php";
require_once "functions.php";

$sets = getPublicSets($conn);

require_once "../includes/header.php";
?>

<h1>Bộ Flashcard công khai</h1>

<?php if (isLoggedIn()): ?>
    <p><a href="create.php">Tạo bộ Flashcard</a></p>
<?php endif; ?>

<?php if (mysqli_num_rows($sets) > 0): ?>

    <?php while ($set = mysqli_fetch_assoc($sets)): ?>

        <div>
            <h2><?php echo htmlspecialchars($set["title"]); ?></h2>

            <p>
                Môn học:
                <?php echo htmlspecialchars($set["subject"]); ?>
            </p>

            <p>
                <?php echo htmlspecialchars($set["description"]); ?>
            </p>

            <a href="detail.php?id=<?php echo $set["id"]; ?>">
                Xem bộ thẻ
            </a>
        </div>

        <hr>

    <?php endwhile; ?>

<?php else: ?>

    <p>Hiện chưa có bộ Flashcard công khai nào.</p>

<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
