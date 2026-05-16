
<?php
session_start();
include "db.php";

/* ================= USER ID VALIDATION ================= */
if (!isset($_SESSION['id'])) {
    echo json_encode(["reply" => "Session expired. Please log in again."]);
    exit();
}
$user_id = $_SESSION['id']; 

/* ================= INPUT & IMAGE HANDLER ================= */
$image_base64 = null;
$imagePath = null;
$mimeType = "image/jpeg"; // Default fallback
$message = "";
$chat_id = null;

// Check if multipart/form-data payload (with image file) is received
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $mimeType = $_FILES['image']['type'];
    
    // Convert image to Base64 binary text for Gemini API
    $imgData = file_get_contents($fileTmpPath);
    $image_base64 = base64_encode($imgData);
    
    // Save image to local uploads directory for history logs
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $imagePath = $uploadDir . time() . '_' . $fileName;
    move_uploaded_file($fileTmpPath, $imagePath);
    
    // Get text message from POST parameters
    $message = $_POST['message'] ?? "Explain this image.";
    $chat_id = $_POST['chat_id'] ?? ($_SESSION['chat_id'] ?? null);
} else {
    // Fallback to normal text JSON stream if no image is present
    $data = json_decode(file_get_contents("php://input"), true);
    $message = $data['message'] ?? "";
    $chat_id = $data['chat_id'] ?? ($_SESSION['chat_id'] ?? null);
}

// Fallback session identifier creation block if none is explicitly provided
if (!$chat_id) {
    if (!isset($_SESSION['chat_id'])) {
        $_SESSION['chat_id'] = uniqid("chat_");
    }
    $chat_id = $_SESSION['chat_id'];
} else {
    $_SESSION['chat_id'] = $chat_id;
}

/* ================= LOAD USER MEMORY ================= */
$memoryText = "";
$stmt = $conn->prepare("
    SELECT message, reply 
    FROM chats 
    WHERE user_id=? 
    ORDER BY id DESC 
    LIMIT 10
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

/* ================= CHAT MEMORY (CURRENT CHAT) ================= */
$historyText = "";
$stmt = $conn->prepare("
    SELECT message, reply 
    FROM chats 
    WHERE user_id=? AND chat_id=? 
    ORDER BY id DESC LIMIT 6
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

/* ================= GEMINI API SETUP ================= */
$apiKey = "API_KEY_HERE"; // REPLACE WITH YOUR GOOGLE CLOUD API KEY
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=$apiKey";

/* ================= FINAL PROMPT STRUCTURE ================= */
$finalPrompt = "System Memory:\n$memoryText\n\nCurrent Chat Context History:\n$historyText\nUser: $message\nAI:";

/* ================= BUILD API MULTIMODAL REQUEST PAYLOAD ================= */
$parts = [];

// Text content wrap instruction
if($message != ""){
    $parts[] = ["text" => $finalPrompt];
}

// Multimodal inline data engine insertion block
if($image_base64){
    $parts[] = [
        "inline_data" => [
            "mime_type" => $mimeType,
            "data" => $image_base64
        ]
    ];
}

$postData = [
    "contents" => [[
        "parts" => $parts
    ]]
];

/* ================= CURL EXECUTION ================= */
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

// FIXES CONNECTION BREAKDOWNS ON LOCAL HOST MACHINES (XAMPP / MAMP)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    echo json_encode(["reply" => "cURL Connectivity Broken Code: " . $error_msg]);
    curl_close($ch);
    exit();
}
curl_close($ch);

$result = json_decode($response, true);
$reply = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

if (!$reply) {
    // Helpful debugging layer if Google returns an API auth error structure instead
    if (isset($result['error']['message'])) {
        $reply = "API Core Gateway Error: " . $result['error']['message'];
    } else {
        $reply = "No response received from Gemini API pipeline components.";
    }
}

/* ================= TEXT FORMATTING ================= */
$reply = nl2br($reply);
$reply = preg_replace('/\*\*(.*?)\*\//', '<b>$1</b>', $reply);
$reply = preg_replace('/^# (.*)$/m', '<h3>$1</h3>', $reply);
$reply = preg_replace('/```(.*?)```/s',
    '<pre style="background:#0b1220;padding:10px;border-radius:10px;overflow:auto;color:#38bdf8;">$1</pre>',
    $reply
);

/* ================= GENERATE CHAT TITLE ================= */
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

/* ================= SAVE DATA TO DATABASE ================= */
$stmt = $conn->prepare("
    INSERT INTO chats (user_id, message, reply, image_path, chat_title, chat_id)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isssss", $user_id, $message, $reply, $imagePath, $chat_title, $chat_id);
$stmt->execute();

/* ================= JSON OUTPUT RESPONSE ================= */
echo json_encode([
    "reply" => $reply
]);
?>

