<?php
session_start();
include "db.php";

// Ensure user is logged in securely
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['id']; // Cast type for SQL optimization stability
$dataPoints = [];

// Secure optimized loop data mapping layer
for($i=6; $i>=0; $i--){
    $date = date("Y-m-d", strtotime("-$i days"));
    
    // Using clean syntax interpolation safely with integer parameters
    $q = "SELECT COUNT(*) as total FROM chats 
          WHERE user_id = $user_id 
          AND DATE(created_at) = '$date'";

    $res = $conn->query($q);
    if($res) {
        $row = $res->fetch_assoc();
        $dataPoints[] = (int)$row['total'];
    } else {
        $dataPoints[] = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ilmexa AI Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ================= GLOBAL LAYOUT SYSTEM (FIXES FOOTER AT BASE) ================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: radial-gradient(circle at top, #0f2027, #0b1220, #050814);
    color: #e2e8f0;
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* ================= BACKGROUND GLOW ================= */
body::before, body::after {
    content: "";
    position: fixed;
    width: 500px;
    height: 500px;
    filter: blur(120px);
    z-index: -1;
}

body::before {
    background: rgba(56, 189, 248, 0.18);
    top: -150px;
    left: -150px;
}

body::after {
    background: rgba(34, 197, 94, 0.14);
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
    background: rgba(10, 15, 25, 0.85);
    backdrop-filter: blur(22px);
    border-bottom: 1px solid rgba(56, 189, 248, 0.15);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

header::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 65%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #38bdf8, #22c55e, #facc15, transparent);
    opacity: 0.6;
}

.logo img {
    height: 55px;
    transition: 0.35s ease;
}

.logo img:hover {
    transform: scale(1.05);
    filter: drop-shadow(0 0 18px rgba(56,189,248,0.8));
}

nav {
    display: flex;
    align-items: center;
    gap: 18px;
}

nav a {
    text-decoration: none;
    color: #cbd5f5;
    font-size: 14px;
    font-weight: bold;
    position: relative;
    padding: 6px 4px;
    transition: 0.3s ease;
}

nav a:hover {
    color: #38bdf8;
    text-shadow: 0 0 10px rgba(56,189,248,0.3);
}

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

/* ================= CONTAINER ================= */
.container {
    padding: 40px 80px;
    flex: 1;
}

.welcome {
    text-align: center;
    font-size: 42px;
    margin-bottom: 40px;
    font-weight: bold;
    animation: fadeInUp 1s ease;
}

.welcome span {
    background: linear-gradient(270deg, #38bdf8, #22c55e, #facc15, #a855f7, #38bdf8);
    background-size: 800% 800%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientMove 6s ease infinite;
}

@keyframes gradientMove {
    0% { background-position: 0% 50% }
    50% { background-position: 100% 50% }
    100% { background-position: 0% 50% }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ================= PREMIUM MODERN UPGRADED CARDS ================= */
.cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    perspective: 1200px;
    margin-bottom: 50px;
}

.card {
    padding: 40px 30px;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    color: white;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transform-style: preserve-3d;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    animation: floatCard 5s ease-in-out infinite;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
}

.card:nth-child(1)::before { background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(14, 165, 233, 0.05)); border: 1px solid rgba(56, 189, 248, 0.3); }
.card:nth-child(2)::before { background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(22, 163, 74, 0.05)); border: 1px solid rgba(34, 197, 94, 0.3); }
.card:nth-child(3)::before { background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(147, 51, 234, 0.05)); border: 1px solid rgba(168, 85, 247, 0.3); }

.card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 20px;
    z-index: 0;
    backdrop-filter: blur(15px);
    transition: 0.4s ease;
}

.card > * {
    position: relative;
    z-index: 2;
}

.card h3 {
    font-size: 22px;
    margin-bottom: 12px;
    font-weight: bold;
    letter-spacing: 0.5px;
    transition: 0.3s;
}

.card:nth-child(1):hover h3 { color: #38bdf8; text-shadow: 0 0 15px rgba(56, 189, 248, 0.6); }
.card:nth-child(2):hover h3 { color: #22c55e; text-shadow: 0 0 15px rgba(34, 197, 94, 0.6); }
.card:nth-child(3):hover h3 { color: #a855f7; text-shadow: 0 0 15px rgba(168, 85, 247, 0.6); }

.card p {
    font-size: 14px;
    color: #94a3b8;
    line-height: 1.5;
}

.card:hover {
    transform: translateY(-12px) scale(1.03);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
}

.card:nth-child(1):hover::before { border-color: #38bdf8; background: linear-gradient(135deg, rgba(56, 189, 248, 0.25), rgba(14, 165, 233, 0.08)); }
.card:nth-child(2):hover::before { border-color: #22c55e; background: linear-gradient(135deg, rgba(34, 197, 94, 0.25), rgba(22, 163, 74, 0.08)); }
.card:nth-child(3):hover::before { border-color: #a855f7; background: linear-gradient(135deg, rgba(145, 66, 219, 0.25), rgba(147, 51, 234, 0.08)); }

@keyframes floatCard {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0px); }
}

/* ================= GRAPH SECTION ================= */
.graph {
    margin-top: 50px;
    padding: 30px;
    border-radius: 18px;
    background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
    border: 1px solid rgba(56, 189, 248, 0.15);
    max-width: 950px;
    margin-left: auto;
    margin-right: auto;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 45px rgba(0,0,0,0.4);
}

/* ================= FOOTER ================= */
footer {
    padding: 25px 20px;
    text-align: center;
    font-weight: bold;
    background: #060b13;
    border-top: 1px solid rgba(56, 189, 248, 0.15);
    color: #94a3b8;
    font-size: 13px;
    letter-spacing: 0.6px;
    position: relative;
    margin-top: auto;
}

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

footer span {
    color: #38bdf8;
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="logo-animated.svg" alt="Logo">
    </div>
    <nav>
        <a href="dashboard.php" style="color: #38bdf8;">Dashboard</a>
        <a href="chat.php">Chat</a>
        <a href="schedule.php">Schedule</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="welcome">
        Welcome, <span><?php echo htmlspecialchars($_SESSION['user'] ?? 'User'); ?></span> 👋
    </div>

    <div class="cards">
        <div class="card" onclick="location.href='chat.php'">
            <h3>🧠 Chat with AI</h3>
            <p>Talk with AI instantly & share images for complex analysis</p>
        </div>
        <div class="card" onclick="location.href='history.php'">
            <h3>📜 Chat History</h3>
            <p>Review and download your past learning sessions</p>
        </div>
        <div class="card" onclick="location.href='schedule.php'">
            <h3>📅 Study Schedule</h3>
            <p>Create, manage and optimize your academic tasks</p>
        </div>
    </div>

    <div class="graph">
        <h3 style="color:#38bdf8; margin-bottom:20px; text-align:center; font-size:18px;">📊 Weekly AI Chat Activity</h3>
        <canvas id="chart"></canvas>
    </div>
</div>

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
            backgroundColor: "rgba(34,197,94,0.08)",
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: "#38bdf8",
            pointBorderColor: "#fff",
            pointBorderWidth: 2
        }]
    },
    options:{
        responsive: true,
        plugins:{
            legend:{
                labels:{ color:"#fff", font: { family: 'Poppins', weight: 'bold' } }
            }
        },
        scales:{
            x:{
                ticks:{ color:"#94a3b8", font: { family: 'Poppins' } },
                grid:{ color:"rgba(255,255,255,0.03)" }
            },
            y:{
                ticks:{ color:"#94a3b8", font: { family: 'Poppins' }, precision: 0 },
                grid:{ color:"rgba(255,255,255,0.03)" }
            }
        }
    }
});
</script>
</body>
</html>