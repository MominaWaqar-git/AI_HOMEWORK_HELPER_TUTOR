<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

if (!isset($_GET['chat_id'])) {
    die("Invalid request");
}

$chat_id = $_GET['chat_id'];

/* ================= DELETE CHAT ================= */
$stmt = $conn->prepare("DELETE FROM chats WHERE chat_id=? AND user_id=?");
$stmt->bind_param("si", $chat_id, $user_id);

if ($stmt->execute()) {
    header("Location: history.php?msg=deleted");
    exit();
} else {
    echo "Delete failed";
}
?>