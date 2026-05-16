
<?php
session_start();
include "db.php";

// Tight operational check for active sessions
if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
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
/* ================= GLOBAL LAYOUT CONFIG (FIXES FOOTER & VIEWPORT) ================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: radial-gradient(circle at top, #0f2027, #0b1220, #050814);
    color: #e2e8f0;
    min-height: 100vh; 
    display: flex;
    flex-direction: column;
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

/* ================= NAV ================= */
nav {
    display: flex;
    align-items: center;
    gap: 18px;
}

nav a {
    text-decoration: none;
    color: #cbd5f5;
    font-weight: bold;
    font-size: 14px;
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

.exit-btn {
    padding: 8px 16px;
    border-radius: 10px;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: white;
    transition: 0.3s ease;
}

.exit-btn:hover {
    box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
    transform: translateY(-1px);
}

/* ================= PREMIUM CHAT CONTAINER ================= */
.chat-box {
    flex: 1; 
    overflow-y: auto;
    padding: 40px 80px;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 190px); 
}

/* Base Message Structure */
.msg {
    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.6;
    font-size: 15px;
    padding: 16px 20px;
    margin: 12px 0;
    border-radius: 16px;
    max-width: 75%;
    width: fit-content;        /* Ensures container adjusts to content size */
    display: flex;             /* Added internal flex settings */
    flex-direction: column;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

/* User Message Design */
.user {
    background: linear-gradient(135deg, #38bdf8, #22c55e);
    color: #040811;
    font-weight: 500;
    margin-left: auto;
    border-bottom-right-radius: 2px;
}

/* AI Response Message Design */
.bot {
    background: #0e1726; 
    border: 1px solid rgba(56, 189, 248, 0.25);
    color: #f1f5f9; 
    margin-right: auto;
    border-bottom-left-radius: 2px;
    border-left: 4px solid #38bdf8; 
}

.chat-img-preview {
    max-width: 100%;
    max-height: 250px;
    border-radius: 12px;
    margin-top: 12px;
    display: block;
    border: 2px solid rgba(56, 189, 248, 0.3);
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}

/* ================= MODERN CONSOLIDATED INPUT AREA ================= */
.input-area {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 24px 80px;
    background: rgba(8, 12, 22, 0.85);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(56, 189, 248, 0.15);
}

input#input {
    flex: 1;
    padding: 15px;
    border-radius: 12px;
    outline: none;
    background: #070c14;
    color: #ffffff;
    border: 1px solid rgba(56, 189, 248, 0.2);
    font-size: 14px;
    transition: 0.3s ease;
}

input#input:focus {
    border-color: #38bdf8;
    background: #0a1220;
    box-shadow: 0 0 15px rgba(56, 189, 248, 0.15);
}

/* ================= VIBRANT MEDIA AND INTERACTION CONTROL ACTIONS ================= */
button, .file-btn {
    padding: 14px 20px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, background 0.3s, border-color 0.3s, box-shadow 0.3s;
}

button:hover, .file-btn:hover {
    transform: translateY(-2px);
}

button:active, .file-btn:active {
    transform: translateY(1px);
}

.file-btn {
    background: rgba(56, 189, 248, 0.1);
    border: 1px solid rgba(56, 189, 248, 0.4);
    color: #38bdf8;
}

.file-btn:hover {
    background: rgba(56, 189, 248, 0.2);
    border-color: #38bdf8;
    box-shadow: 0 0 12px rgba(56, 189, 248, 0.3);
}

.voice-btn {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.4);
    color: #22c55e;
}

.voice-btn:hover {
    background: rgba(34, 197, 94, 0.2);
    border-color: #22c55e;
    box-shadow: 0 0 12px rgba(34, 197, 94, 0.3);
}

.voice-btn.listening {
    animation: pulseGlow 1.5s infinite;
    background: #ef4444;
    border-color: #ef4444;
    color: white;
}

@keyframes pulseGlow {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.send-btn {
    background: linear-gradient(135deg, #38bdf8, #22c55e);
    color: #040811;
    padding: 14px 24px;
}

.send-btn:hover {
    box-shadow: 0 0 18px rgba(56, 189, 248, 0.4);
}

.save-btn {
    background: linear-gradient(135deg, #a855f7, #6366f1);
    color: white;
}

.save-btn:hover {
    box-shadow: 0 0 18px rgba(168, 85, 247, 0.4);
}

/* Inside Card Utilities */
.mini-copy {
    background: transparent;
    border: none;
    color: #38bdf8;
    cursor: pointer;
    font-size: 14px;
    margin-top: 8px;
    display: inline-flex;
    width: max-content;
    padding: 4px 0;
    transition: 0.2s;
}

.mini-copy:hover {
    transform: scale(1.05);
    color: #22c55e;
}

/* ================= PRESTIGE FOOTER ================= */
footer {
    padding: 20px;
    text-align: center;
    font-weight: bold;
    background: #060b13; 
    border-top: 1px solid rgba(56, 189, 248, 0.15);
    color: #94a3b8;
    font-size: 13px;
    letter-spacing: 0.6px;
    margin-top: auto; 
    position: relative;
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
        <span style="color: #38bdf8; font-weight: bold; margin-right: 10px;">Ayesha 👤</span>
        <a href="dashboard.php">Dashboard</a>
        <a href="history.php">History</a>
        <a href="logout.php" class="exit-btn">Exit</a>
    </nav>
</header>

<div class="chat-box" id="chatBox">
    <div class="msg bot">
        <div>👋 Hi! I am Ilmexa AI. Ask me anything or upload an image for analysis.</div>
    </div>
</div>

<div class="input-area">
    <input id="input" placeholder="Type message..." onkeydown="if(event.key === 'Enter') send()">

    <label class="file-btn" title="Upload Image">
        📷
        <input type="file" id="imageInput" accept="image/*" onchange="sendImage()" style="display: none;">
    </label>

    <button class="voice-btn" id="voiceBtn" onclick="startVoice()">🎤</button>
    <button class="send-btn" onclick="send()">Send</button>
    <button onclick="saveChat()" class="save-btn">💾 Save Chat</button>
</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Learning Assistant
</footer>

<script>
const box = document.getElementById("chatBox");

/* ================= STREAM TEXT PAYLOAD ================= */
async function send(){
    let input = document.getElementById("input");
    let text = input.value.trim();
    if(text==="") return;

    // FIXED: Formatted layout alignment structure with a dedicated text block element
    box.innerHTML += `
        <div class="msg user">
            <div style="width: 100%; display: block;">${escapeHtml(text)}</div>
            <button class="mini-copy" onclick="copyMsg(this)" title="Copy Message" style="color:#040811;">📋 Copy</button>
        </div>`;

    input.value="";
    box.scrollTop = box.scrollHeight;

    showLoading();

    try {
        let res = await fetch("gemini.php", {
            method:"POST",
            headers:{ "Content-Type":"application/json" },
            body: JSON.stringify({message:text})
        });
        let data = await res.json();
        removeLoading();
        renderBotReply(data.reply);
    } catch (e) {
        removeLoading();
        renderBotReply("Server Error: Unable to process data stream.");
    }
}

/* ================= MULTIMODAL FILE ATTACHMENT ================= */
async function sendImage(){
    let fileInput = document.getElementById("imageInput");
    let file = fileInput.files[0];
    if(!file) return;

    let inputField = document.getElementById("input");
    let customText = inputField.value.trim();
    if(customText === "") customText = "Explain this image.";

    let reader = new FileReader();
    reader.onload = function(e){
        box.innerHTML += `
            <div class="msg user">
                <div style="width: 100%; display: block;">${escapeHtml(customText)}</div>
                <img src="${e.target.result}" class="chat-img-preview" />
            </div>`;
        box.scrollTop = box.scrollHeight;
    }
    reader.readAsDataURL(file);

    let formData = new FormData();
    formData.append("image", file);
    formData.append("message", customText);

    inputField.value = "";
    showLoading();

    try {
        let res = await fetch("gemini.php", {
            method:"POST",
            body: formData
        });
        let data = await res.json();
        removeLoading();
        renderBotReply(data.reply);
    } catch(e) {
        removeLoading();
        renderBotReply("Error: Multimodal pipeline file verification broke down.");
    }
    fileInput.value = ""; 
}

/* ================= SYSTEM ENGINE FUNCTIONS ================= */
function showLoading(){
    let loading = document.createElement("div");
    loading.className = "msg bot";
    loading.innerHTML = "<div>🤖 Thinking...</div>";
    loading.id = "loadingMsg";
    box.appendChild(loading);
    box.scrollTop = box.scrollHeight;
}

function removeLoading(){
    let item = document.getElementById("loadingMsg");
    if(item) item.remove();
}

function renderBotReply(replyText){
    let botDiv = document.createElement("div");
    botDiv.className = "msg bot";

    let content = document.createElement("div");
    content.style.width = "100%";
    content.innerHTML = replyText ? replyText.trim() : "";

    let btn = document.createElement("button");
    btn.className = "mini-copy";
    btn.innerHTML = "📋 Copy Response";
    btn.title = "Copy Response";
    btn.onclick = function(){
       navigator.clipboard.writeText(content.innerText);
       alert("Response copied to clipboard!");
    };

    botDiv.appendChild(content);
    botDiv.appendChild(btn);
    box.appendChild(botDiv);
    box.scrollTop = box.scrollHeight;
}

function escapeHtml(text){
    return text
        .replaceAll("&","&amp;")
        .replaceAll("<","&lt;")
        .replaceAll(">","&gt;");
}

// FIXED: Properly targets the sibling text component safely across blocks
function copyMsg(btn){
    let msgBox = btn.parentElement.querySelector("div");
    navigator.clipboard.writeText(msgBox.innerText)
    .then(() => alert("Message copied!"))
    .catch(() => alert("❌ Copy failed"));
}

function saveChat(){
    let chat = box.innerText;
    let blob = new Blob([chat], { type: "text/plain" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "ilmexa_chat_session.txt";
    link.click();
}

/* ================= SPEECH INTEGRATION PROTOCOL ================= */
function startVoice(){
    const voiceBtn = document.getElementById("voiceBtn");
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if(!SpeechRecognition){
        alert("Your device browser framework doesn't map modern Web Speech API protocols.");
        return;
    }

    let recognition = new SpeechRecognition();
    recognition.lang = "en-US";
    recognition.interimResults = false;

    recognition.onstart = function() {
        voiceBtn.classList.add("listening");
    };

    recognition.onerror = function() {
        voiceBtn.classList.remove("listening");
    };

    recognition.onend = function() {
        voiceBtn.classList.remove("listening");
    };

    recognition.onresult = function(event){
        let resultText = event.results[0][0].transcript;
        document.getElementById("input").value = resultText;
    };

    recognition.start();
}
</script>
</body>
</html>

