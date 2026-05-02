<?php
session_start();
include 'db.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $errors[] = "Invalid request!";
}

/* INPUTS */
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$captcha = $_POST['captcha'] ?? '';

/* 1. EMPTY CHECK */
if (empty($email)) {
    $errors[] = "Email is required!";
}

if (empty($password)) {
    $errors[] = "Password is required!";
}

if (empty($captcha)) {
    $errors[] = "Captcha is required!";
}

/*  EMAIL FORMAT */
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format!";
}

/* 3. CAPTCHA CHECK */
if (!isset($_SESSION['captcha']) || !empty($captcha) && $captcha !== $_SESSION['captcha']) {
    $errors[] = "Captcha incorrect!";
}

/* 4. USER CHECK (only if email ok) */
$user = null;

if (empty($errors)) {

    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $errors[] = "Account not found. Please register first!";
    } else {
        $user = $result->fetch_assoc();

        /* 5. PASSWORD CHECK */
        if (!password_verify($password, $user['password'])) {
            $errors[] = "Incorrect password!";
        }
    }
}

/* IF ERRORS EXIST */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: login.php");
    exit();
}

/* SUCCESS */
$_SESSION['user'] = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['id'] = $user['id'];

unset($_SESSION['captcha']);

header("Location: dashboard.php");
exit();
?>