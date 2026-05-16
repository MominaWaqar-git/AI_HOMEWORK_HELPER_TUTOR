<?php
session_start();
include "db.php";

// 1. Strict Session Verification Check
if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// 2. Validate request parameters
if (!isset($_GET['chat_id']) || empty(trim($_GET['chat_id']))) {
    header("Location: history.php");
    exit();
}

$chat_id = trim($_GET['chat_id']);

/* ================= STEP 1: PHYSICAL STORAGE CLEANUP (OPTIONAL BIT) ================= */
// Pehle un saari entries ko check karein jahan is chat session mein images upload hui thin
$img_stmt = $conn->prepare("SELECT image_path FROM chats WHERE chat_id=? AND user_id=? AND image_path IS NOT NULL AND image_path != ''");
$img_stmt->bind_param("si", $chat_id, $user_id);
$img_stmt->execute();
$img_res = $img_stmt->get_result();

while ($row = $img_res->fetch_assoc()) {
    $file = $row['image_path'];
    if (file_exists($file)) {
        unlink($file); // Server directory se physical file remove kar dega
    }
}
$img_stmt->close();


/* ================= STEP 2: DATABASE RECORDS TRUNCATION ================= */
$stmt = $conn->prepare("DELETE FROM chats WHERE chat_id=? AND user_id=?");
$stmt->bind_param("si", $chat_id, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    // Redirect cleanly with state message parameter
    header("Location: history.php?status=success");
    exit();
} else {
    $stmt->close();
    $conn->close();
    header("Location: history.php?status=error");
    exit();
}
?>