<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ilmexa AI Homework Helper</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: 'Poppins', sans-serif;
    background: radial-gradient(circle at top, #0f2027, #0b1220, #050814);
    color: #e2e8f0;
    overflow-x: hidden;
}

/* ================= BACKGROUND GLOW ================= */
body::before,
body::after {
    content: "";
    position: fixed;
    width: 500px;
    height: 500px;
    filter: blur(120px);
    z-index: -1;
}

body::before {
    background: rgba(56,189,248,0.18);
    top: -150px;
    left: -150px;
}

body::after {
    background: rgba(34,197,94,0.14);
    bottom: -160px;
    right: -160px;
}

/* ================= HEADER ================= */
header {
    position: sticky;
    top: 0;
    z-index: 100;

    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 14px 80px;

    /* glass premium background */
    background: linear-gradient(
        135deg,
        rgba(10, 15, 25, 0.75),
        rgba(15, 23, 42, 0.55)
    );

    backdrop-filter: blur(22px);

    border-bottom: 1px solid rgba(56,189,248,0.15);

    /* subtle shadow */
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}

/* 🌟 bottom glow line */
header::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 65%;
    height: 2px;
    background: linear-gradient(
        90deg,
        transparent,
        #38bdf8,
        #22c55e,
        #facc15,
        transparent
    );
    opacity: 0.6;
}

/* ================= LOGO ================= */
.logo img {
    height: 60px;
    transition: 0.35s ease;
}

.logo img:hover {
    transform: scale(1.1);
    filter: drop-shadow(0 0 18px rgba(56,189,248,0.8));
}

/* ================= NAV ================= */
nav {
    display: flex;
    align-items: center;
    gap: 18px;
}

/* links */
nav a {
    text-decoration: none;
    color: #cbd5f5;
    font-weight: 500;
    font-size: 14px;

    position: relative;
    padding: 6px 4px;

    transition: 0.3s ease;
}

/* hover glow text */
nav a:hover {
    color: #38bdf8;
    text-shadow: 0 0 10px rgba(56,189,248,0.3);
}

/* animated underline */
nav a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0%;
    height: 2px;

    background: linear-gradient(90deg, #38bdf8, #22c55e, #facc15);

    transition: width 0.35s ease;
}

nav a:hover::after {
    width: 100%;
}

/* active feel (optional future use) */
nav a.active {
    color: #38bdf8;
    border-bottom: 2px solid #38bdf8;
}

/* ================= HERO ================= */
.hero {
    text-align: center;
    padding: 110px 20px 60px;
}

.hero h2 {
    font-size: 64px;
    background: linear-gradient(90deg, #38bdf8, #22c55e, #facc15);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: floatText 4s ease-in-out infinite;
}

@keyframes floatText {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.hero p {
    font-size: 18px;
    max-width: 750px;
    margin: 18px auto;
    color: #bcd5ff;
    line-height: 1.7;
}

/* BUTTON */
.btn {
    display: inline-block;
    margin-top: 30px;
    padding: 14px 42px;
    background: linear-gradient(135deg, #38bdf8, #22c55e, #facc15);
    color: black;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 10px 30px rgba(56,189,248,0.25);
}

.btn:hover {
    transform: scale(1.08);
}

/* ================= FEATURES ================= */
.features {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 80px;
    flex-wrap: wrap;
}

/* CARD */
.card {
    width: 300px;
    padding: 32px;
    border-radius: 20px;
    text-align: center;

    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(56,189,248,0.15);
    backdrop-filter: blur(20px);

    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: 0.4s ease;
}

/* GLASS SWEEP */
.card::before {
    content: "";
    position: absolute;
    top: 0;
    left: -180%;
    width: 160%;
    height: 100%;

    background: linear-gradient(
        120deg,
        transparent,
        rgba(56,189,248,0.14),
        rgba(34,197,94,0.08),
        rgba(255,255,255,0.06),
        transparent
    );

    transform: skewX(-20deg);
    transition: left 1.4s ease-in-out;
}

.card:hover::before {
    left: 180%;
}

/* BORDER GLOW */
.card::after {
    content: "";
    position: absolute;
    inset: 0;
    padding: 1px;
    border-radius: 20px;
    background: linear-gradient(
        120deg,
        transparent,
        rgba(56,189,248,0.6),
        rgba(34,197,94,0.4),
        transparent
    );
    opacity: 0;
    transition: 0.5s;
    pointer-events: none;
}

.card:hover::after {
    opacity: 1;
}

/* HOVER */
.card:hover {
    transform: translateY(-14px) scale(1.04);
    box-shadow: 0 30px 70px rgba(0,0,0,0.55);
}

/* ACTIVE CLICK */
.card.active {
    transform: scale(1.1) translateY(-10px);
    border: 1px solid #38bdf8;
    box-shadow: 0 0 35px rgba(56,189,248,0.6);
}

/* TEXT */
.card h3 {
    color: #38bdf8;
    margin-bottom: 10px;
    font-size: 18px;
}

.card p {
    color: #cbd5f5;
    font-size: 14px;
    opacity: 0.9;
}

/* ================= FOOTER (FIXED + PREMIUM) ================= */
footer {
    margin-top: 120px;
    padding: 35px 20px;
    text-align: center;

    background: linear-gradient(
        135deg,
        rgba(10, 15, 25, 0.75),
        rgba(15, 23, 42, 0.55)
    );

    backdrop-filter: blur(22px);
    border-top: 1px solid rgba(56,189,248,0.2);

    color: #94a3b8;
    font-size: 13px;
    letter-spacing: 0.6px;

    position: relative;
    overflow: hidden;
}

/* top glow line */
footer::before {
    content: "";
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #38bdf8, #22c55e, transparent);
    opacity: 0.6;
}

/* glow blob */
footer::after {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(56,189,248,0.08);
    filter: blur(80px);
    bottom: -150px;
    right: -100px;
}

footer span {
    color: #38bdf8;
    font-weight: bold;
    text-shadow: 0 0 10px rgba(56,189,248,0.4);
}

/* HOME */
#home {
    height: 10px;
}
</style>

</head>

<body>

<header>

    <div class="logo">
        <img src="logo-animated.svg" alt="Ilmexa Logo">
    </div>

    <nav>
        <a href="#home">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>

        <?php if(isset($_SESSION['user'])) { ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php } ?>
    </nav>

</header>

<div id="home"></div>

<div class="hero">

    <h2>Smart AI Learning Assistant</h2>

    <p>
        Ilmexa helps students solve homework, understand concepts, and learn faster
        with AI-powered explanations in simple language.
    </p>

    <?php if(!isset($_SESSION['user'])) { ?>
        <a href="login.php" class="btn">Get Started</a>
    <?php } else { ?>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    <?php } ?>

    <div class="features">

        <div class="card">
            <h3>📘 Homework Help</h3>
            <p>Step-by-step solutions for every subject in seconds.</p>
        </div>

        <div class="card">
            <h3>🧠 Smart Learning</h3>
            <p>AI simplifies complex topics into easy explanations.</p>
        </div>

        <div class="card">
            <h3>⚡ Instant Answers</h3>
            <p>Get fast AI responses anytime you need help.</p>
        </div>

    </div>

</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Built for Smart Learning & Future Education
</footer>

<script>
const cards = document.querySelectorAll('.card');

cards.forEach(card => {
    card.addEventListener('click', () => {
        cards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');

        setTimeout(() => {
            card.classList.remove('active');
        }, 900);
    });
});
</script>

</body>
</html>