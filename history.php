<?php
session_start();
include "db.php";

if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Secure prepared statement aggregating unique chat blocks safely
$stmt = $conn->prepare("
    SELECT chat_id, chat_title, MAX(created_at) as last_date
    FROM chats
    WHERE user_id=?
    GROUP BY chat_id
    ORDER BY last_date DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat History - Ilmexa AI</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Poppins', sans-serif;
}

body{
    background: radial-gradient(circle at top,#0f2027,#0b1220,#050814);
    color:#e2e8f0;
    min-height: 100vh;
    display: flex;          /* Added: Enables Flexbox on body */
    flex-direction: column; /* Added: Arranges children vertically */
}

/* ================= HEADER ================= */
header{
    position:sticky;
    top:0;
    z-index: 100;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 80px; /* Aligned header layout across systems */
    background:rgba(10,15,25,0.75);
    backdrop-filter:blur(22px);
    border-bottom:1px solid rgba(56,189,248,0.15);
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
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

.logo img{
    height:55px;
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
.container{
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 80px;
    flex: 1; /* Added: Pushes the footer down to fill structural void */
}

h1{
    text-align:center;
    margin-bottom:40px;
    color:#38bdf8;
    font-size: 32px;
}

/* ================= CARDS FRAME ================= */
.card{
    background:rgba(255,255,255,0.03);
    padding:22px 25px;
    border-radius:16px;
    margin-bottom:20px;
    border:1px solid rgba(56,189,248,0.12);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: 0.3s ease;
    backdrop-filter: blur(10px);
}

.card:hover{
    transform: translateY(-4px);
    border-color: rgba(56,189,248,0.3);
    background: rgba(255,255,255,0.05);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.card-details {
    flex: 1;
    padding-right: 20px;
}

.card-details h3 {
    font-size: 18px;
    margin-bottom: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 500px;
}

.date{
    font-size:12px;
    color:#94a3b8;
}

.card-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* ================= INTERACTION COMPONENTS ================= */
.btn{
    padding:10px 16px;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
    transition: 0.2s ease;
    display: inline-flex;
    align-items: center;
}

.btn:hover {
    transform: scale(1.04);
}

.btn:active {
    transform: scale(0.97);
}

.view-btn{
    background: linear-gradient(135deg, #38bdf8, #0ea5e9);
    color: black;
}

.delete-btn{
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.delete-btn:hover {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}

.empty{
    text-align:center;
    margin-top:100px;
    color:#94a3b8;
    font-size: 16px;
}

/* ================= FOOTER ================= */
footer {
    font-weight: bold;
    padding: 35px 20px;
    text-align: center;
    background: linear-gradient(135deg, rgba(10, 15, 25, 0.75), rgba(15, 23, 42, 0.55));
    backdrop-filter: blur(22px);
    border-top: 1px solid rgba(56,189,248,0.2);
    color: #94a3b8;
    font-size: 13px;
    letter-spacing: 0.6px;
    position: relative;
    overflow: hidden;
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
    font-weight: bolder;
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <img src="logo-animated.svg" alt="Ilmexa AI">
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="chat.php">Chat</a>
        <a href="history.php" style="color: #38bdf8;">History</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">

    <h1>📜 Chat History</h1>

    <?php if($result->num_rows > 0){ ?>
        <?php while($row = $result->fetch_assoc()){ 
            // Secure fallback text logic if title is set to empty space
            $raw_title = trim($row['chat_title']);
            $display_title = (!empty($raw_title)) ? htmlspecialchars($raw_title) : "Untitled Discussion Thread";
        ?>

            <div class="card">
                <div class="card-details">
                    <h3 style="color:#38bdf8;">💬 <?php echo $display_title; ?></h3>
                    <div class="date">🕒 <?php echo date("d M Y, h:i A", strtotime($row['last_date'])); ?></div>
                </div>

                <div class="card-actions">
                    <a href="view_chat.php?chat_id=<?php echo urlencode($row['chat_id']); ?>">
                        <button class="btn view-btn">👁 View</button>
                    </a>

                    <a href="delete_chat.php?chat_id=<?php echo urlencode($row['chat_id']); ?>"
                       onclick="return confirmDelete(event)">
                        <button class="btn delete-btn">🗑 Delete</button>
                    </a>
                </div>
            </div>

        <?php } ?>
    <?php } else { ?>
        <div class="empty">No chat history found on your account 😔</div>
    <?php } ?>

</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Learning Platform
</footer>

<script>
function confirmDelete(event){
    event.preventDefault();
    
    // Get closest parent anchor tags dynamically to prevent element reference breakdown
    const anchor = event.target.closest("a");
    if (!anchor) return false;

    let confirmAction = confirm("⚠ Operational Alert:\nAre you sure you want to permanently purge this chat thread?");

    if(confirmAction){
        window.location.href = anchor.href;
    }
    return false;
}
</script>

</body>
</html>