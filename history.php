<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

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
<title>Chat History</title>

<style>
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

/* HEADER */
header{
    position:sticky;
    top:0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 50px;
    background:rgba(10,15,25,0.7);
    backdrop-filter:blur(20px);
    border-bottom:1px solid rgba(56,189,248,0.2);
}

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

.logo img{
    height:55px;
}

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

/* CONTAINER */
.container{
    padding:40px;
}

h1{
    text-align:center;
    margin-bottom:30px;
    color:#38bdf8;
}

/* CARD */
.card{
    background:rgba(255,255,255,0.04);
    padding:18px;
    border-radius:14px;
    margin-bottom:15px;
    border:1px solid rgba(56,189,248,0.15);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

/* BUTTONS */
.view-btn{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    background:#38bdf8;
    color:black;
    font-weight:bold;
    cursor:pointer;
    margin-top:10px;
}

.delete-btn{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    background:#ef4444;
    color:white;
    font-weight:bold;
    cursor:pointer;
    margin-top:10px;
}

.delete-btn:hover{
    transform:scale(1.05);
}

/* DATE */
.date{
    font-size:12px;
    color:#94a3b8;
    margin-top:5px;
}

/* EMPTY */
.empty{
    text-align:center;
    margin-top:80px;
    color:#94a3b8;
}

footer {
    font-weight: bold;
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
    font-weight: bold;
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

<div class="container">

<h1>📜 Chat History</h1>

<?php if($result->num_rows > 0){ ?>

    <?php while($row = $result->fetch_assoc()){ ?>

        <div class="card">

            <h3 style="color:#38bdf8;">
                💬 <?php echo $row['chat_title'] ?? 'Untitled Chat'; ?>
            </h3>

            <div class="date">🕒 <?php echo $row['last_date']; ?></div>

            <!-- VIEW -->
            <a href="view_chat.php?chat_id=<?php echo urlencode($row['chat_id']); ?>">
                <button class="view-btn">👁 View</button>
            </a>

            <!-- DELETE (FIXED ID ISSUE) -->
            <a href="delete_chat.php?chat_id=<?php echo urlencode($row['chat_id']); ?>"
               onclick="return confirmDelete(event)">
                <button class="delete-btn">🗑 Delete</button>
            </a>

        </div>

    <?php } ?>

<?php } else { ?>

    <div class="empty">No chat history found 😔</div>

<?php } ?>

</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Learning Platform
</footer>

<script>
function confirmDelete(event){
    event.preventDefault();

    let ok = confirm("⚠ Are you sure you want to delete this chat?");

    if(ok){
        window.location.href = event.target.closest("a").href;
    }

    return false;
}
</script>

</body>
</html>