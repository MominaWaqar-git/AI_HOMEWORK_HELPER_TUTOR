<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
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
    font-family:Poppins;
}

body{
    background: radial-gradient(circle at top,#0f2027,#0b1220,#050814);
    color:#e2e8f0;
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

/* glow line */
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

/* logo */
.logo img{
    height:50px;
}

/* title */
.topbar h2{
    font-size:16px;
    color:#cbd5f5;
}

/* buttons */
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
    max-width:900px;
    margin:auto;
    padding:30px;
}

/* message bubble */
.msg{
    padding:14px 16px;
    margin:10px 0;
    border-radius:14px;
    max-width:75%;
    line-height:1.6;

    white-space:pre-wrap;   /* ⭐ IMPORTANT FIX */
    word-break:break-word;
}

/* USER */
.user{
    background:linear-gradient(135deg,#38bdf8,#22c55e);
    color:black;
    margin-left:auto;
    border-bottom-right-radius:4px;
    text-align:right;
}

/* BOT */
.bot{
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(56,189,248,0.2);
    margin-right:auto;
    border-bottom-left-radius:4px;
}

/* TIME */
.time{
    font-size:11px;
    color:#94a3b8;
    margin-top:6px;
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

<!-- HEADER -->
<div class="topbar">

    <div class="logo">
        <img src="logo-animated.svg">
    </div>

    <h2>💬 Chat View</h2>

    <div class="actions">
        <button class="btn copy-btn" onclick="copyChat()">📋 Copy</button>
        <button class="btn save-btn" onclick="saveChat()">💾 Save</button>
        <a href="history.php"><button class="btn back-btn">⬅ Back</button></a>
    </div>

</div>

<!-- CHAT -->
<div class="chat-box" id="chatBox">

<?php if($result->num_rows > 0){ ?>

    <?php while($row = $result->fetch_assoc()){ ?>

        <div class="msg user">
            🧑 <?php echo nl2br(htmlspecialchars($row['message'])); ?>
        </div>

        <div class="msg bot">
        <?php
            $text = $row['reply'];

            /* 1. headings ### -> H3 */
            $text = preg_replace('/### (.*)/', '<h3 style="color:#38bdf8;margin-top:10px;">$1</h3>', $text);

            /* 2. bold <b> tags */
            $text = str_replace(["<b>", "</b>"], ["<strong>", "</strong>"], $text);

            /* 3. markdown **bold** */
            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);

            /* 4. <br>*/
            $text = str_replace(["<br>", "<br/>", "<br />"], "", $text);

            /* 5. final output */
            echo nl2br($text);
?>
            <div class="time">🕒 <?php echo $row['created_at']; ?></div>
        </div>

    <?php } ?>

<?php } else { ?>

    <div class="empty">No chat found 😔</div>

<?php } ?>

</div>

<!-- FOOTER -->
<footer>
    © 2026 Ilmexa AI — Smart Chat System
</footer>

<script>

/* COPY */
function copyChat(){
    let text = document.getElementById("chatBox").innerText;
    navigator.clipboard.writeText(text);
    alert("📋 Chat copied!");
}

/* SAVE */
function saveChat(){
    let text = document.getElementById("chatBox").innerText;

    let blob = new Blob([text], {type:"text/plain"});
    let a = document.createElement("a");

    a.href = URL.createObjectURL(blob);
    a.download = "ilmexa_chat.txt";
    a.click();
}

</script>

</body>
</html>