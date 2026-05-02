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
<title>Ilmexa AI Chat</title>

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
    height:100vh;
    display:flex;
    flex-direction:column;
    overflow:hidden;
}

/* ================= HEADER ================= */
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


.exit-btn{
    padding:8px 14px;
    border-radius:10px;
    background:linear-gradient(135deg,#ef4444,#f97316);
    color:white;
}

/* ================= CHAT ================= */
.chat-box{
    flex:1;
    overflow-y:auto;
    padding:25px 40px;
}

.msg{
    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.6;
    font-size: 14px;
    padding: 14px;
    margin: 10px 0;
    border-radius: 12px;
    max-width: 70%;
}

.user{
    background: linear-gradient(135deg,#38bdf8,#22c55e);
    color: black;
    margin-left: auto;
}

.bot{
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(56,189,248,0.2);
}
/* ================= INPUT ================= */
.input-area{
    display:flex;
    align-items:center;
    gap:10px;
    padding:15px 40px;
    background:rgba(10,15,25,0.6);
    backdrop-filter:blur(20px);
    border-top:1px solid rgba(56,189,248,0.15);
}

input{
    flex:1;
    padding:14px;
    border-radius:12px;
    border:none;
    outline:none;
    background:rgba(255,255,255,0.05);
    color:white;
}

/* buttons */
button{
    padding:12px 16px;
    border:none;
    border-radius:12px;
    cursor:pointer;
    font-weight:bold;
}

/* send */
.send-btn{
    background:linear-gradient(135deg,#38bdf8,#22c55e,#facc15);
}

/* voice */
.voice-btn{
    background:linear-gradient(135deg,#a855f7,#38bdf8);
    color:white;
}

/* image */
.file-btn{
    background:linear-gradient(135deg,#f97316,#ef4444);
    color:white;
}

/* hidden file */
input[type="file"]{
    display:none;
}


/* ================= FOOTER ================= */
footer {
    
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


/* AI reply styling */
.bot{
    
    padding: 14px;
    border-radius: 12px;
}

.msg.bot{
    position:relative;
}

.msg.bot::after{
    content:"";
    display:inline-block;
    width:6px;
    height:6px;
    margin-left:5px;
    border-radius:50%;
    background:#38bdf8;
    animation:blink 1s infinite;
}

@keyframes blink{
    0%,100%{opacity:0.2}
    50%{opacity:1}
}

.copy-btn, .save-btn{
    padding:10px 14px;
    border:none;
    border-radius:10px;
    margin:5px;
    cursor:pointer;
    font-weight:bold;
    color:white;
    transition:0.3s;
}

.mini-copy{
    float:right;
    margin-left:10px;
    background:transparent;
    border:none;
    color:#38bdf8;
    cursor:pointer;
    font-size:14px;
    transition:0.2s;
}

.mini-copy:hover{
    transform:scale(1.3);
    color:#22c55e;
}

.save-btn{
    background:linear-gradient(135deg,#a855f7,#fb7185);
}

.save-btn:hover{
    transform:scale(1.05);
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
        <span><?php echo $_SESSION['user']; ?></span>
        <a href="dashboard.php">Dashboard</a>
        <a href="history.php">History</a>
        <a href="logout.php" class="exit-btn">Exit</a>
    </nav>

</header>

<!-- CHAT BOX -->
<div class="chat-box" id="chatBox">
    <div class="msg bot">👋 Hi! I am Ilmexa AI. Ask me anything.</div>
</div>

<!-- INPUT AREA -->
<div class="input-area">

    <input id="input" placeholder="Type message...">

    <!-- IMAGE UPLOAD -->
    <label class="file-btn">
        📷
        <input type="file" id="imageInput" accept="image/*" onchange="sendImage()">
    </label>

    <!-- VOICE -->
    <button class="voice-btn" onclick="startVoice()">🎤</button>
    <!-- SEND -->
    <button class="send-btn" onclick="send()">Send</button>
    <button onclick="saveChat()" class="save-btn">💾 Save Chat</button>


</div>



<!-- FOOTER -->
<footer>
    © 2026 Ilmexa AI — Smart Learning Assistant
</footer>

<script>

/* ================= TEXT SEND ================= */
async function send(){

    let input = document.getElementById("input");
    let text = input.value.trim();
    if(text==="") return;

    let box = document.getElementById("chatBox");

    box.innerHTML += `
        <div class="msg user">
        ${escapeHtml(text)}
        <button class="mini-copy" onclick="copyMsg(this)">📋</button>
        </div>`;

    input.value="";

    let loading = document.createElement("div");
    loading.className = "msg bot";
    loading.innerHTML = "🤖 Thinking...";
    loading.id = "loadingMsg";
    box.appendChild(loading);

    box.scrollTop = box.scrollHeight;

    let res = await fetch("gemini.php", {
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body: JSON.stringify({message:text})
    });

    let data = await res.json();

    document.getElementById("loadingMsg").remove();

    let formatted = formatText(data.reply);

    let botDiv = document.createElement("div");
    botDiv.className = "msg bot";

    // content wrapper (important for formatting)
    let content = document.createElement("div");
    content.innerHTML = formatted;

    // copy button
    let btn = document.createElement("button");
    btn.className = "mini-copy";
    btn.innerHTML = "📋";
    btn.onclick = function(){
       navigator.clipboard.writeText(content.innerText);
       alert("Copied!");
    };

    botDiv.appendChild(content);
    botDiv.appendChild(btn);

    box.appendChild(botDiv);
}

function formatText(text){
    if(!text) return "";

    return text
        .replace(/\\n/g, "<br>")   
        .replace(/\n/g, "<br>")    
        .trim();
}

function escapeHtml(text){
    return text
        .replaceAll("&","&amp;")
        .replaceAll("<","&lt;")
        .replaceAll(">","&gt;");
}

function renderSafeHTML(text){
    return text
        .replace(/```([\s\S]*?)```/g, "<pre>$1</pre>") 
        .replace(/\\n/g, "<br>");
}

function copyMsg(btn){
    let msgBox = btn.parentElement;

    let text = msgBox.innerText.replace("📋", "").trim();

    navigator.clipboard.writeText(text)
    .then(() => {
        alert("📋 Message copied!");
    })
    .catch(() => {
        alert("❌ Copy failed");
    });
}

function saveChat(){
    let chat = document.getElementById("chatBox").innerText;

    let blob = new Blob([chat], { type: "text/plain" });
    let link = document.createElement("a");

    link.href = URL.createObjectURL(blob);
    link.download = "ilmexa_chat.txt";

    link.click();
}

/* ================= VOICE INPUT ================= */
function startVoice(){

    let recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = "en-US";

    recognition.onresult = function(event){
        document.getElementById("input").value = event.results[0][0].transcript;
    }

    recognition.start();
}

/* ================= IMAGE UPLOAD ================= */
async function sendImage(){

    let fileInput = document.getElementById("imageInput");
    let file = fileInput.files[0];

    let formData = new FormData();
    formData.append("image", file);

    let box = document.getElementById("chatBox");

    box.innerHTML += `<div class="msg user">📷 Image sent</div>`;

    let res = await fetch("gemini.php", {
        method:"POST",
        body: formData
    });

    let data = await res.json();

    box.innerHTML += `<div class="msg bot">${data.reply}</div>`;
    box.scrollTop = box.scrollHeight;
}

</script>

</body>
</html>