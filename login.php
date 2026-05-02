<?php
session_start();

if (!empty($_SESSION['errors'])) {

    echo "<div style='
        background: rgba(255,0,0,0.15);
        border:1px solid rgba(255,0,0,0.4);
        color:#ff6b6b;
        padding:10px;
        border-radius:10px;
        margin:15px auto;
        text-align:center;
        width:80%;
    '>";

    foreach ($_SESSION['errors'] as $err) {
        echo "• " . htmlspecialchars($err) . "<br>";
    }

    echo "</div>";

    unset($_SESSION['errors']);
}
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Poppins;
}

body{
    background: radial-gradient(circle at top,#0f2027,#0b1220,#050814);
    color:#fff;
    overflow-x:hidden;
}

/* ================= HEADER ================= */
header {
    position: sticky;
    top: 0;
    font-weight: bold;
    z-index: 100;

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
    font-weight: bold;
    font-size: 14px;

    position: relative;
    padding: 6px 4px;

    transition: 0.3s ease;
}

/* hover glow text */
nav a:hover {
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

/* active feel (optional future use) */
nav a.active {
    color: #38bdf8;
    font-weight: bold;
    border-bottom: 2px solid #38bdf8;
}


/* ================= LOGIN BOX ================= */
.container{
    height:85vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.box{
    width:380px;
    padding:35px;
    border-radius:20px;
    background:rgba(255,255,255,0.04);
    backdrop-filter:blur(20px);
    border:1px solid rgba(56,189,248,0.2);
    box-shadow:0 30px 80px rgba(0,0,0,0.5);
}

h2{
    text-align:center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom:10px;
    background:linear-gradient(90deg,#38bdf8,#22c55e,#facc15);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

p{
    text-align:center;
    font-size:13px;
    color:#94a3b8;
    margin-bottom:15px;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:none;
    outline:none;
    background:rgba(255,255,255,0.05);
    color:#fff;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#38bdf8,#22c55e,#facc15);
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
}

.links{
    text-align:center;
    margin-top:15px;
    font-size:13px;
}

a{
    color:#38bdf8;
    text-decoration:none;
}

.password-box {
    position: relative;
}

.password-box input {
    width: 100%;
    padding: 12px;
    padding-right: 45px;
    border-radius: 10px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.05);
    color: #fff;
}

.toggle-eye {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    color: #94a3b8;
    transition: 0.3s;
}

.toggle-eye:hover {
    color: #38bdf8;
}

/* ================= FOOTER ================= */
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
    <div class="logo">
        <img src="logo-animated.svg" alt="logo">
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<!-- LOGIN BOX -->
<div class="container">

<div class="box">

    <h2>Login</h2>
    <p>If you are first time user then register first</p>

    <form method="POST" action="login_process.php">

        <input type="email" name="email" placeholder="Email" required>
        <div class="password-box">
            <input type="password" id="password" name="password" placeholder="Password" required>

            <span class="toggle-eye" onclick="togglePassword()"><i class="fa-solid fa-eye-slash"></i></span>
        </div>

        <div style="margin:15px 0;text-align:center;">

            <img src="captcha.php?rand=<?php echo time(); ?>"
            style="
                border-radius:10px;
                height:60px;
                width:160px;
                border:2px solid #38bdf8;
                margin-bottom:10px;
                ">

            <br><br>

            <input type="text"
                   name="captcha"
                   placeholder="Enter Captcha"
                   required>
        </div>

        <button type="submit">Login</button>

    </form>

    <div class="links">
        New user? <a href="register.php">Create account</a>
    </div>

</div>

</div>

<footer>
    © 2026 <span>Ilmexa AI</span> — Smart Learning Platform
</footer>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.querySelector(".toggle-eye i");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        password.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
</script>

</body>