<?php
session_start();
include "db.php";

if (!isset($_SESSION['user']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_schedule'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $date = $_POST['date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    if (!empty($title) && !empty($date) && !empty($start) && !empty($end)) {
        $stmt = $conn->prepare("
            INSERT INTO student_schedules 
            (user_id, title, task_description, schedule_date, start_time, end_time) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssss", $user_id, $title, $desc, $date, $start, $end);

        if ($stmt->execute()) {
            $message = "<div class='alert success'>🎉 Schedule task created successfully!</div>";
        } else {
            $message = "<div class='alert error'>❌ Operational Error updating log.</div>";
        }
        $stmt->close();
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM student_schedules WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: schedule.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM student_schedules WHERE user_id = ? ORDER BY schedule_date ASC, start_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$schedules = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Study Schedule - Ilmexa AI</title>
    <style>
        /* [Integrating base styles tailored above] */
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { background: radial-gradient(circle at top,#0f2027,#0b1220,#050814); color:#e2e8f0; min-height: 100vh; display: flex; flex-direction: column; }
        
        header { position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 14px 80px; background: rgba(10,15,25,0.85); backdrop-filter: blur(22px); border-bottom: 1px solid rgba(56,189,248,0.15); }
        header::after { content: ""; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 65%; height: 2px; background: linear-gradient(90deg, transparent, #38bdf8, #22c55e, #facc15, transparent); opacity: 0.6; }
        nav { display: flex; align-items: center; gap: 18px; }
        nav a { text-decoration: none; color: #cbd5f5; font-weight: bold; font-size: 14px; padding: 6px 4px; transition: 0.3s; }
        nav a:hover { color: #38bdf8; text-shadow: 0 0 10px rgba(56,189,248,0.3); }

        .schedule-container { flex: 1; width: 100%; max-width: 1400px; margin: 0 auto; padding: 40px 50px; }
        
        /* Fixed Aspect Grid Ratio for wider form view */
        .grid { display: grid; grid-template-columns: 1.3fr 1.7fr; gap: 35px; margin-top: 20px; }

        .form-card, .list-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid rgba(56, 189, 248, 0.15);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            padding: 35px;
            border-radius: 18px;
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }
        .form-card:hover, .list-card:hover { border-color: rgba(56,189,248,0.35); transform: translateY(-3px); }

        h2 { color: #38bdf8; margin-bottom: 25px; font-size: 22px; }
        label { font-size: 12px; color: #94a3b8; display: block; margin-bottom: 6px; font-weight: 600; }
        
        input, textarea { width: 100%; padding: 14px; margin-bottom: 20px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: white; outline: none; transition: 0.3s; }
        input:focus, textarea:focus { border-color: #38bdf8; background: rgba(255,255,255,0.08); }

        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #38bdf8, #22c55e); border: none; border-radius: 10px; color: black; font-weight: bold; cursor: pointer; transition: 0.2s; }
        button:hover { transform: scale(1.02); box-shadow: 0 0 15px rgba(56,189,248,0.4); }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
        th { color: #38bdf8; font-size: 13px; text-transform: uppercase; }
        
        .btn-delete { color: #ef4444; text-decoration: none; font-weight: bold; font-size: 13px; padding: 6px 12px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); transition: 0.2s; }
        .btn-delete:hover { background: #ef4444; color: white; box-shadow: 0 0 10px rgba(239, 68, 68, 0.4); }

        .alert { padding: 14px; border-radius: 10px; margin-bottom: 25px; font-weight: bold; }
        .success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #22c55e; }
        .error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; }

        footer { width: 100%; font-weight: bold; padding: 30px 20px; text-align: center; background: #060b13; border-top: 1px solid rgba(56, 189, 248, 0.15); color: #94a3b8; font-size: 13px; margin-top: auto; }
        footer span { color: #38bdf8; }
    </style>
</head>
<body>

<header>
    <div class="logo"><img src="logo-animated.svg" alt="Ilmexa AI" style="height:55px;"></div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="chat.php">Chat</a>
        <a href="history.php">History</a>
        <a href="schedule.php" style="color: #38bdf8;">Schedule</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="schedule-container">
    <?php echo $message; ?>
    <div class="grid">
        <div class="form-card">
            <h2>📅 Create Task</h2>
            <form action="schedule.php" method="POST">
                <label>Task Title</label>
                <input type="text" name="title" placeholder="e.g., Malware Analysis Lab" required>
                <label>Description Details</label>
                <textarea name="description" rows="4" placeholder="Mention rules..." required></textarea>
                <label>Target Execution Date</label>
                <input type="date" name="date" required>
                <div style="display:flex; gap:15px;">
                    <div style="flex:1;"><label>Start Time</label><input type="time" name="start_time" required></div>
                    <div style="flex:1;"><label>End Time</label><input type="time" name="end_time" required></div>
                </div>
                <button type="submit" name="add_schedule">Save Schedule Plan</button>
            </form>
        </div>

        <div class="list-card">
            <h2>📜 Your Active Timelines</h2>
            <?php if ($schedules->num_rows == 0): ?>
                <p style="color:#94a3b8;">No tasks mapped to your academic roadmap yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Task Properties</th>
                            <th>Date Target</th>
                            <th>Time Slot</th>
                            <th>Control</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $schedules->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong style="color: #38bdf8;"><?php echo htmlspecialchars($row['title']); ?></strong><br>
                                    <span style="font-size:12px; color:#94a3b8;"><?php echo htmlspecialchars($row['task_description']); ?></span>
                                </td>
                                <td><?php echo date("d M, Y", strtotime($row['schedule_date'])); ?></td>
                                <td style="color: #22c55e; font-weight:500;">
                                    <?php echo date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])); ?>
                                </td>
                                <td><a href="schedule.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Drop task?')">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>© 2026 <span>Ilmexa AI</span> — Smart Learning Platform</footer>
</body>
</html>