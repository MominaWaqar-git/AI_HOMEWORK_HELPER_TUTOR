<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ilmexa AI Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    overflow-x:hidden;
}

/* ================= BACKGROUND GLOW ================= */
body::before,
body::after{
    content:"";
    position:fixed;
    width:500px;
    height:500px;
    filter:blur(120px);
    z-index:-1;
}

body::before{
    background:rgba(56,189,248,0.18);
    top:-150px;
    left:-150px;
}

body::after{
    background:rgba(34,197,94,0.14);
    bottom:-160px;
    right:-160px;
}

/* ================= HEADER ================= */
header{
    position:sticky;
    top:0;
    z-index:100;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 80px;

    background:linear-gradient(
        135deg,
        rgba(10, 15, 25, 0.75),
        rgba(15, 23, 42, 0.55)
    );

    backdrop-filter:blur(22px);
    border-bottom:1px solid rgba(56,189,248,0.15);
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

/* glow line */
header::after{
    content:"";
    position:absolute;
    bottom:0;
    left:50%;
    transform:translateX(-50%);
    width:65%;
    height:2px;
    background:linear-gradient(90deg,transparent,#38bdf8,#22c55e,#facc15,transparent);
    opacity:0.6;
}

.logo img{
    height:55px;
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

/* active feel  */
nav a.active {
    color: #38bdf8;
    border-bottom: 2px solid #38bdf8;
}

/* ================= CONTAINER ================= */
.container{
    padding:40px 80px;
}

/* ================= WELCOME ================= */
.welcome{
    text-align:center;
    font-size:42px;
    margin-bottom:30px;
    font-weight:bold;
}

.welcome span{
    background:linear-gradient(90deg,#38bdf8,#22c55e,#facc15);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* ================= CARDS ================= */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:25px;
}

/* PREMIUM CARD */
.card{
    padding:28px;
    border-radius:20px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(56,189,248,0.15);
    backdrop-filter:blur(18px);
    cursor:pointer;
    transition:0.4s;
    position:relative;
    overflow:hidden;
}

/* gradient hover sweep */
.card::before{
    content:"";
    position:absolute;
    top:0;
    left:-150%;
    width:150%;
    height:100%;
    background:linear-gradient(120deg,transparent,rgba(56,189,248,0.25),rgba(34,197,94,0.15),transparent);
    transform:skewX(-20deg);
    transition:0.8s;
}

.card:hover::before{
    left:150%;
}

.card:hover{
    transform:translateY(-12px) scale(1.05);
    box-shadow:0 30px 70px rgba(0,0,0,0.5);
}

/* ICON TITLE */
.card h3{
    font-size:18px;
    margin-bottom:10px;
    color:#38bdf8;
}

/* CARD COLORS VARIETY */
.card:nth-child(1) h3{ color:#38bdf8; }
.card:nth-child(2) h3{ color:#22c55e; }
.card:nth-child(3) h3{ color:#facc15; }
.card:nth-child(4) h3{ color:#a855f7; }
.card:nth-child(5) h3{ color:#fb7185; }

.card p{
    color:#cbd5f5;
    font-size:14px;
    opacity:0.9;
}

/* ================= GRAPH ================= */
.graph{
    margin-top:50px;
    padding:25px;
    border-radius:20px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(56,189,248,0.15);
}

/* ================= FOOTER ================= */
footer{
    margin-top:120px;
    padding:35px 20px;
    text-align:center;
    font-weight:bold;

    background:linear-gradient(
        135deg,
        rgba(10, 15, 25, 0.75),
        rgba(15, 23, 42, 0.55)
    );

    backdrop-filter:blur(22px);
    border-top:1px solid rgba(56,189,248,0.2);
    color:#94a3b8;
    font-size:13px;
}

footer span{
    color:#38bdf8;
}
</style>
</head>

<body>

<!-- HEADER -->
<header>
    <div class="logo">
        <img src="logo-animated.svg">
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="chat.php">Chat</a>
        <a href="history.php">History</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<!-- MAIN -->
<div class="container">

    <div class="welcome">
        Welcome, <span><?php echo $_SESSION['user']; ?></span> 👋
    </div>

    <!-- CARDS -->
    <div class="cards">

        <div class="card" onclick="location.href='chat.php'">
            <h3>🧠 Chat with AI</h3>
            <p>Talk with AI instantly</p>
        </div>

        <div class="card" onclick="location.href='history.php'">
            <h3>📜 Chat History</h3>
            <p>View past conversations</p>
        </div>

        <div class="card" onclick="location.href='save.php'">
            <h3>💾 Save Chat</h3>
            <p>Save important answers</p>
        </div>

        <div class="card" onclick="location.href='homework.php'">
            <h3>📘 Homework Help</h3>
            <p>Step-by-step solutions</p>
        </div>

        <div class="card" onclick="location.href='quiz.php'">
            <h3>⚡ Solve Quiz</h3>
            <p>MCQs with explanation</p>
        </div>

    </div>

    <!-- GRAPH -->
    <div class="graph">
        <h3 style="color:#38bdf8;margin-bottom:10px;">📊 Activity Graph</h3>
        <canvas id="chart"></canvas>
    </div>

</div>

<!-- FOOTER -->
<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Learning Platform
</footer>

<script>
new Chart(document.getElementById("chart"), {
    type: "line",
    data: {
        labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
        datasets: [{
            label: "Activity",
            data: [3,5,4,8,6,7,9],
            borderColor: "#38bdf8",
            tension: 0.4
        }]
    }
});
</script>

</body>
</html>