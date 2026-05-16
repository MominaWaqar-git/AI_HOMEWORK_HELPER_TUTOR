<?php
session_start();
include "db.php";

// Standardized tight session validation across all secure interfaces
if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$chat_id = $_GET['chat_id'] ?? null;
$user_id = $_SESSION['id'];

if (!$chat_id) {
    die("Invalid Chat ID");
}

$stmt = $conn->prepare("
    SELECT message, reply, created_at
    FROM chats
    WHERE user_id=? AND chat_id=?
    ORDER BY id ASC
");

$stmt->bind_param("is", $user_id, $chat_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Chat - Ilmexa AI</title>

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
    display: flex;          
    flex-direction: column; 
}

/* ================= HEADER ================= */
.topbar{
    position:sticky;
    top:0;
    z-index:1000;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 25px;
    background:rgba(10,15,25,0.75);
    backdrop-filter:blur(22px);
    border-bottom:1px solid rgba(56,189,248,0.2);
}

.topbar::after{
    content:"";
    position:absolute;
    bottom:0;
    left:50%;
    transform:translateX(-50%);
    width:60%;
    height:2px;
    background:linear-gradient(90deg,transparent,#38bdf8,#22c55e,#facc15,transparent);
    opacity:0.6;
}

.logo img{
    height:50px;
}

.topbar h2{
    font-size:16px;
    color:#cbd5f5;
}

.actions{
    display:flex;
    gap:8px;
}

.btn{
    padding:9px 14px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.05);
}

.copy-btn{
    background:linear-gradient(135deg,#38bdf8,#22c55e);
    color:black;
}

.save-btn{
    background:linear-gradient(135deg,#a855f7,#38bdf8);
    color:white;
}

.back-btn{
    background:rgba(255,255,255,0.1);
    color:white;
}

/* ================= CHAT ================= */
.chat-box{
    width: 100%;
    max-width:900px;
    margin:auto;
    padding:30px;
    flex: 1; 
}

.msg{
    padding:14px 16px;
    margin:15px 0;
    border-radius:14px;
    max-width:75%;
    min-width: 180px; 
    line-height:1.6;
    white-space: pre-wrap; 
    word-break:break-word;
}

/* USER Bubble Alignment */
.user{
    background:linear-gradient(135deg,#38bdf8,#22c55e);
    color:black;
    margin-left:auto;
    border-bottom-right-radius:4px;
    text-align: left;
}

/* BOT Bubble */
.bot{
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(56,189,248,0.2);
    margin-right:auto;
    border-bottom-left-radius:4px;
}

.time{
    font-size:11px;
    color:#94a3b8;
    margin-top:8px;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding-top: 4px;
}

.empty{
    text-align:center;
    margin-top:80px;
    color:#94a3b8;
}

/* ================= FOOTER ================= */
footer {
    font-weight: bold;
    padding: 35px 20px;
    text-align: center;
    background: linear-gradient(135deg, rgba(10, 15, 25, 0.75), rgba(15, 23, 42, 0.55));
    backdrop-filter: blur(22px);
    border-top: 1px solid rgba(56,189,248,0.2);
    color:#94a3b8;
    margin-top: auto;
}
footer span{
    color: #38bdf8;
}
</style>
</head>
<body>

<div class="topbar">
    <div class="logo"><img src="logo-animated.svg" alt="Logo"></div>
    <h2>Ilmexa Premium Chat Vault</h2>
    <div class="actions">
        <button class="btn copy-btn" onclick="copyChat()">Copy Logs</button>
        <button class="btn save-btn" onclick="saveChat()">Export Document</button>
        <a href="history.php"><button class="btn back-btn">← Back</button></a>
    </div>
</div>

<div class="chat-box" id="chatBox">

<?php if ($result->num_rows > 0) { ?>
    <?php while($row = $result->fetch_assoc()) { ?>
        
        <div class="msg user">
            <?php echo htmlspecialchars($row['message']); ?>
            <div class="time" style="color: rgba(0,0,0,0.55);">🕒 <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></div>
        </div>

        <div class="msg bot">
            <?php 
                $text = $row['reply'];
                $text = preg_replace('/### (.*)/', '<h3 style="color:#38bdf8; margin: 10px 0 5px 0;">$1</h3>', $text);
                $text = preg_replace('/\\*\\*(.*?)\\*\\*/', '<strong>$1</strong>', $text);
                $text = str_replace(["<b>", "</b>"], ["<strong>", "</strong>"], $text);
                echo $text;
            ?>
            <div class="time">🕒 <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></div>
        </div>

    <?php } ?>
<?php } else { ?>
    <div class="empty">No chat logs found for this session 😔</div>
<?php } ?>

</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Chat System
</footer>

<script>
function copyChat(){
    let text = document.getElementById("chatBox").innerText;
    navigator.clipboard.writeText(text)
    .then(() => alert("📋 Full chat log copied to clipboard!"))
    .catch(() => alert("❌ Copy failed."));
}

function saveChat(){
    let text = document.getElementById("chatBox").innerText;
    let blob = new Blob([text], { type: "text/plain" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "ilmexa_chat_vault_export.txt";
    link.click();
}
</script>
</body>
</html>