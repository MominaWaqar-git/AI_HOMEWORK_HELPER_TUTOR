<?php
session_start();
include "db.php";

/* ================= CHAT SESSION (PERSISTENT MEMORY) ================= */
if(!isset($_SESSION['chat_id'])){
    $_SESSION['chat_id'] = uniqid("chat_");
}
$chat_id = $_SESSION['chat_id'];

/* ================= INPUT ================= */
$data = json_decode(file_get_contents("php://input"), true);
$message = $data['message'] ?? "";

/* ================= USER ID ================= */
$user_id = $_SESSION['id']; 

/* ================= IMAGE ================= */
$image_base64 = null;
$imagePath = null;

/* ================= LOAD USER MEMORY (IMPORTANT ADDITION) ================= */
/* last 20 messages user-wide memory */
$memoryText = "";

$stmt = $conn->prepare("
    SELECT message, reply 
    FROM chats 
    WHERE user_id=? 
    ORDER BY id DESC 
    LIMIT 20
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$memory = [];

while($row = $res->fetch_assoc()){
    $memory[] = "User: " . $row['message'];
    $memory[] = "AI: " . strip_tags($row['reply']);
}

$memoryText = implode("\n", array_reverse($memory));

/* ================= CHAT MEMORY (CURRENT CHAT ONLY) ================= */
$historyText = "";

$stmt = $conn->prepare("
    SELECT message, reply 
    FROM chats 
    WHERE user_id=? AND chat_id=? 
    ORDER BY id DESC LIMIT 10
");
$stmt->bind_param("is", $user_id, $chat_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];

while($row = $result->fetch_assoc()){
    $history[] = "User: " . $row['message'];
    $history[] = "AI: " . strip_tags($row['reply']);
}

$historyText = implode("\n", array_reverse($history));

/* ================= GEMINI API ================= */
$apiKey = "YOUR_API_KEY_HERE";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

/* ================= FINAL PROMPT (MEMORY + CHAT) ================= */
$finalPrompt =
"System Memory:\n$memoryText\n\nCurrent Chat:\n$historyText\nUser: $message\nAI:";

/* ================= BUILD REQUEST ================= */
$parts = [];

if($message != ""){
    $parts[] = ["text" => $finalPrompt];
}

if($image_base64){
    $parts[] = [
        "inline_data" => [
            "mime_type" => "image/jpeg",
            "data" => $image_base64
        ]
    ];
}

$postData = [
    "contents" => [[
        "parts" => $parts
    ]]
];

/* ================= CURL ================= */
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

$reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "No response";

/* ================= FORMAT ================= */
$reply = nl2br($reply);
$reply = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $reply);
$reply = preg_replace('/^# (.*)$/m', '<h3>$1</h3>', $reply);

$reply = preg_replace('/```(.*?)```/s',
    '<pre style="background:#0b1220;padding:10px;border-radius:10px;overflow:auto;color:#38bdf8;">$1</pre>',
    $reply
);

/* ================= CHAT TITLE ================= */
$chat_title = null;

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM chats WHERE chat_id=?");
$stmt->bind_param("s", $chat_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if($row['total'] == 0){
    $chat_title = substr($message, 0, 30);
    $chat_title = preg_replace('/[^a-zA-Z0-9 ]/', '', $chat_title);
}

/* ================= SAVE CHAT ================= */
$stmt = $conn->prepare("
    INSERT INTO chats (user_id, message, reply, image_path, chat_title, chat_id)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("isssss", $user_id, $message, $reply, $imagePath, $chat_title, $chat_id);
$stmt->execute();

/* ================= OUTPUT ================= */
echo json_encode([
    "reply" => $reply
]);
?>