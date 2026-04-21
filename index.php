<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AI Homework Helper</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth; /* smooth scroll for home */
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color: #e2e8f0;
}

/* HEADER */
header {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 80px;
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(12px);
}

header h1 {
    font-size: 24px;
    color: #38bdf8;
}

/* NAV */
nav a {
    margin-left: 25px;
    text-decoration: none;
    color: #cbd5f5;
    font-weight: 500;
    transition: 0.3s;
}

nav a:hover {
    color: #38bdf8;
}

/* HERO */
.hero {
    text-align: center;
    padding: 120px 20px;
}

.hero h2 {
    font-size: 52px;
    margin-bottom: 15px;
    color: #f1f5f9;
}

.hero p {
    font-size: 18px;
    max-width: 650px;
    margin: auto;
    line-height: 1.6;
    color: #cbd5f5;
}

/* BUTTON */
.btn {
    display: inline-block;
    margin-top: 30px;
    padding: 14px 32px;
    background: linear-gradient(45deg, #ff9b04, #ee9622);
    color: black;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.btn:hover {
    transform: scale(1.05);
}

/* FEATURES */
.features {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 80px;
    flex-wrap: wrap;
}

.card {
    background: rgba(255,255,255,0.07);
    backdrop-filter: blur(10px);
    width: 280px;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-8px);
}

.card h3 {
    color: #38bdf8;
    margin-bottom: 10px;
}

.card p {
    color: #cbd5f5;
    font-size: 14px;
}

/* FOOTER */
footer {
    margin-top: 100px;
    background: rgba(0,0,0,0.4);
    text-align: center;
    padding: 18px;
}

/* HOME anchor section */
#home {
    height: 10px;
}
</style>

</head>

<body>

<header>
    <h1>AI Tutor</h1>

    <nav>
        <!-- HOME scroll to top -->
        <a href="#home">Home</a>

        <!-- direct pages -->
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>

        <?php if(isset($_SESSION['user'])) { ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php } ?>
    </nav>
</header>

<!-- HOME ANCHOR -->
<div id="home"></div>

<!-- HERO -->
<div class="hero">
    <h2>Smart AI Learning Assistant</h2>
    <p>
        Solve homework, understand concepts, and boost your learning
        with a modern AI-powered tutor built for students.
    </p>

    <?php if(!isset($_SESSION['user'])) { ?>
        <a href="login.php" class="btn">Get Started</a>
    <?php } else { ?>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    <?php } ?>

    <div class="features">
        <div class="card">
            <h3>📘 Homework Help</h3>
            <p>Step-by-step solutions for every subject.</p>
        </div>

        <div class="card">
            <h3>🧠 Smart Learning</h3>
            <p>Simple explanations for complex topics.</p>
        </div>

        <div class="card">
            <h3>⚡ Fast AI Answers</h3>
            <p>Instant responses anytime you need help.</p>
        </div>
    </div>
</div>

<footer>
    © 2026 AI Homework Helper
</footer>

</body>
</html>