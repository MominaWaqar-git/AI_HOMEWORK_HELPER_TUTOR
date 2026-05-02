<?php
session_start();
include "db.php";

/* ================= INPUT ================= */
$data = json_decode(file_get_contents("php://input"), true);

$message = $data['message'] ?? "";

/* IMAGE */
$image_base64 = null;

if(isset($_FILES['image'])){
    $image = $_FILES['image']['tmp_name'];
    $image_base64 = base64_encode(file_get_contents($image));
}

/* ================= GEMINI API ================= */
$apiKey = "YOUR_API_KEY_HERE";

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

$parts = [];

if($message != ""){
    $parts[] = ["text" => $message];
}

if($image_base64){
    $parts[] = [
        "inline_data" => [
            "mime_type" => "image/jpeg",
            "data" => $image_base64
        ]
    ];
}

$data = [
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
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

$reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? "No response";


/* ================= SMART FORMATTING ================= */

/* 1. Convert line breaks */
$reply = nl2br($reply);

/* 2. Bold text (**text**) */
$reply = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $reply);

/* 3. Headings (# title) */
$reply = preg_replace('/^# (.*)$/m', '<h3>$1</h3>', $reply);

/* 4. Code blocks ``` */
$reply = preg_replace('/```(.*?)```/s', '<pre style="background:#0b1220;padding:10px;border-radius:10px;overflow:auto;color:#38bdf8;">$1</pre>', $reply);


/* ================= SAVE DB ================= */
$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $conn->prepare("INSERT INTO chats (user_id,message,reply,image_path) VALUES (?,?,?,?)");

$imagePath = null;
$stmt->bind_param("isss", $user_id, $message, $reply, $imagePath);
$stmt->execute();


/* ================= RESPONSE ================= */
echo json_encode([
    "reply" => $reply
]);
?>