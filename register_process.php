<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Invalid request method!";
    header("Location: register.php");
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

/* VALIDATION */
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = "All fields are required!";
    header("Location: register.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format!";
    header("Location: register.php");
    exit();
}

/* CHECK EMAIL EXISTS (SECURE) */
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Email already registered. Please login.";
    header("Location: register.php");
    exit();
}

/* HASH PASSWORD */
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* INSERT USER */
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    $_SESSION['success'] = "Registration successful! Please login.";
    header("Location: login.php");
    exit();
} else {
    $_SESSION['error'] = "Registration failed. Try again!";
    header("Location: register.php");
    exit();
}
?>