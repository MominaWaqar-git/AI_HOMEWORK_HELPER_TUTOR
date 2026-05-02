<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM chats WHERE user_id = '$user_id' ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat History - Ilmexa AI</title>

<style>

/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins;
}

body{
    background: radial-gradient(circle at top,#0f2027,#0b1220,#050814);
    color:#e2e8f0;
}

/* ================= HEADER (same as dashboard style) ================= */
header {
    position: sticky;
    top: 0;
    z-index: 100;
    font-weight: bold;
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
    font-weight: bold;
    position: relative;
    padding: 6px 4px;

    transition: 0.3s ease;
}

/* hover glow text */
nav a:hover {
    font-weight: bold;
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

/* active feel */
nav a.active {
    color: #38bdf8;
    border-bottom: 2px solid #38bdf8;
}


/* ================= CONTAINER ================= */
.container{
    padding:40px 80px;
}

/* TITLE */
h1{
    font-size:34px;
    text-align:center;
    margin-bottom:30px;
    background:linear-gradient(90deg,#38bdf8,#22c55e,#facc15);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* ================= HISTORY CARDS ================= */
.card{
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(56,189,248,0.15);
    padding:20px;
    border-radius:15px;
    margin-bottom:15px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
    background:rgba(255,255,255,0.06);
}

.user-msg{
    color:#38bdf8;
    font-weight:bold;
}

.bot-msg{
    color:#cbd5f5;
    margin-top:5px;
}

/* DATE */
.date{
    font-size:12px;
    color:#94a3b8;
    margin-top:8px;
}

/* EMPTY */
.empty{
    text-align:center;
    margin-top:80px;
    color:#94a3b8;
}

footer {
    margin-top: 120px;
    padding: 35px 20px;
    text-align: center;
    font-weight: bold;

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
    font-weight: bolder;
    text-shadow: 0 0 10px rgba(56,189,248,0.4);
}

</style>

</head>

<body>

<header>

    <div class="logo">Ilmexa AI</div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="chat.php">Chat</a>
        <a href="history.php">History</a>
        <a href="logout.php">Logout</a>
    </nav>

</header>

<div class="container">

    <h1>📜 Your Chat History</h1>

    <?php if ($result->num_rows > 0) { ?>

        <?php while($row = $result->fetch_assoc()) { ?>

            <div class="card">

                <div class="user-msg">🧑 You: <?php echo htmlspecialchars($row['message']); ?></div>

                <div class="bot-msg">🤖 AI: <?php echo htmlspecialchars($row['reply']); ?></div>

                <div class="date">🕒 <?php echo $row['created_at']; ?></div>

            </div>

        <?php } ?>

    <?php } else { ?>

        <div class="empty">
            No chat history found 😔
        </div>

    <?php } ?>

</div>

</body>
</html>