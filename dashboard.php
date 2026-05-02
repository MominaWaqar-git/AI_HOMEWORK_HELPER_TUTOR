<?php
session_start();
include "db.php";

$user_id = $_SESSION['id'];

// last 7 days chat count
$dataPoints = [];

for($i=6;$i>=0;$i--){
    $date = date("Y-m-d", strtotime("-$i days"));

    $q = "SELECT COUNT(*) as total FROM chats 
          WHERE user_id = $user_id 
          AND DATE(created_at) = '$date'";

    $res = $conn->query($q);
    $row = $res->fetch_assoc();

    $dataPoints[] = $row['total'];
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

.welcome{
    text-align:center;
    font-size:45px;
    font-weight:bold;
    margin-bottom:40px;
    animation:fadeInUp 1s ease;
}

/* animated gradient text */
.welcome span{
    background:linear-gradient(270deg,#38bdf8,#22c55e,#facc15,#a855f7,#38bdf8);
    background-size:800% 800%;
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;

    animation:gradientMove 6s ease infinite;
}

/* animations */
@keyframes gradientMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

@keyframes fadeInUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
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

body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#0f172a;
    color:white;
}

/* GRID */
.card-container{
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:20px;
    padding:40px;
}

/* GRID */
.card-container{
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:20px;
    padding:40px;
}

/* optional wrapper */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:30px;
    perspective:1200px;
}

/* CARD BASE */
.card{
    padding:30px;
    border-radius:20px;

    /* ❌ remove glass background */
    background:transparent;

    position:relative;
    overflow:hidden;

    color:white;
    text-align:center;

    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;

    transform-style:preserve-3d;
    transition:0.4s ease;

    animation: floatCard 5s ease-in-out infinite;

    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

/* 🌈 PREMIUM GRADIENT LOOK */
.card::before{
    content:"";
    position:absolute;
    inset:0;
    border-radius:20px;
    z-index:0;
}

/* individual gradients */
.card:nth-child(1)::before{ background:linear-gradient(135deg,#38bdf8,#0ea5e9); }
.card:nth-child(2)::before{ background:linear-gradient(135deg,#22c55e,#16a34a); }
.card:nth-child(3)::before{ background:linear-gradient(135deg,#a855f7,#9333ea); }
.card:nth-child(4)::before{ background:linear-gradient(135deg,#fb7185,#e11d48); }
.card:nth-child(5)::before{ background:linear-gradient(135deg,#14b8a6,#0d9488); }
.card:nth-child(6)::before{ background:linear-gradient(135deg,#f97316,#ea580c); }
.card:nth-child(7)::before{ background:linear-gradient(135deg,#6366f1,#4f46e5); }
.card:nth-child(8)::before{ background:linear-gradient(135deg,#06b6d4,#0284c7); }

/* content layer above gradient */
.card > *{
    position:relative;
    z-index:2;
}

/* ✨ glow border */
.card::after{
    content:"";
    position:absolute;
    inset:0;
    border-radius:20px;
    padding:1px;
    background:linear-gradient(135deg,#ffffff40,#ffffff10);
    -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite:xor;
    mask-composite:exclude;
    opacity:0.5;
    z-index:3;
}

/* 💡 glow hover */
.card:hover{
    transform:translateY(-15px) scale(1.08) rotateX(8deg) rotateY(10deg);
    box-shadow:0 35px 90px rgba(0,0,0,0.6);
}

/* floating animation */
@keyframes floatCard{
    0%{transform:translateY(0px);}
    50%{transform:translateY(-8px);}
    100%{transform:translateY(0px);}
}

/* TEXT */
.card h3{
    font-size:20px;
    margin-bottom:10px;
}

.card p{
    font-size:14px;
    color:#f1f5f9;
}


.graph{
    margin-top:40px;
    padding:20px;
    border-radius:15px;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(56,189,248,0.2);
    max-height: 1000px;
    max-width:1000px;
    margin-left:auto;
    margin-right:auto;
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
        <h3>💾 Saved Chats</h3>
        <p>Access important answers</p>
    </div>

    <div class="card" onclick="location.href='homework.php'">
        <h3>📘 Homework Help</h3>
        <p>Step-by-step explanations</p>
    </div>

    <div class="card" onclick="location.href='quiz.php'">
        <h3>⚡ AI Quiz</h3>
        <p>Generate MCQs instantly</p>
    </div>

    <!-- 🔥 MAKE SCHEDULE (NEW CLEAR BUTTON) -->
    <div class="card" onclick="location.href='schedule.php'">
        <h3>📅 Make Schedule</h3>
        <p>Create your daily study plan</p>
    </div>

    <div class="card" onclick="location.href='important.php'">
        <h3>⭐ Important Chats</h3>
        <p>Mark & view key answers</p>
    </div>

    <div class="card" onclick="location.href='notes.php'">
        <h3>📝 Notes Generator</h3>
        <p>Convert answers into notes</p>
    </div>

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
        labels: ["6d","5d","4d","3d","2d","1d","Today"],
        datasets: [{
            label: "Chats Activity",
            data: <?php echo json_encode($dataPoints); ?>,

            borderColor: "#22c55e",
            backgroundColor: "rgba(34,197,94,0.15)",

            fill:true,
            tension:0.5,

            pointRadius:6,
            pointBackgroundColor:"#38bdf8",
            pointBorderColor:"#fff",
            pointBorderWidth:2
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{
                labels:{ color:"#fff" }
            }
        },
        scales:{
            x:{
                ticks:{ color:"#94a3b8" },
                grid:{ color:"rgba(255,255,255,0.05)" }
            },
            y:{
                ticks:{ color:"#94a3b8" },
                grid:{ color:"rgba(255,255,255,0.05)" }
            }
        }
    }
});
</script>
</body>
</html>