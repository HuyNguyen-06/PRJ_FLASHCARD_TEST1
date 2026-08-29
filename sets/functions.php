<?php

function getPublicSets($conn)
{
    $sql = "SELECT * FROM flashcard_sets
            WHERE visibility='public' AND status='approved'
            ORDER BY created_at DESC";
    return mysqli_query($conn, $sql);
}

function getSetById($conn, $setId)
{
    $sql = "SELECT flashcard_sets.*, users.name AS owner_name
            FROM flashcard_sets
            JOIN users ON flashcard_sets.user_id = users.id
            WHERE flashcard_sets.id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $setId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function createFlashcardSet($conn, $userId, $title, $subject, $description, $visibility, $status)
{
    $sql = "INSERT INTO flashcard_sets
            (user_id, title, subject, description, visibility, status)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt, "isssss",
        $userId, $title, $subject, $description, $visibility, $status
    );

    return mysqli_stmt_execute($stmt);
}

function updateFlashcardSet($conn, $setId, $userId, $title, $subject, $description, $visibility, $status)
{
    $sql = "UPDATE flashcard_sets
            SET title=?, subject=?, description=?, visibility=?, status=?,
                updated_at=CURRENT_TIMESTAMP
            WHERE id=? AND user_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt, "sssssii",
        $title, $subject, $description, $visibility, $status, $setId, $userId
    );

    return mysqli_stmt_execute($stmt);
}

function deleteFlashcardSet($conn, $setId, $userId)
{
    $sql = "DELETE FROM flashcard_sets WHERE id=? AND user_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $setId, $userId);

    return mysqli_stmt_execute($stmt);
}
