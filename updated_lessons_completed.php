<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$lesson_id = intval($_POST['lesson_id'] ?? 0);

// Fetch current progress
$stmt = $conn->prepare("SELECT lessons_completed FROM user_progress WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($lessons_completed);
$stmt->fetch();
$stmt->close();

// 🧠 Example: total lessons available = 10
$total_lessons = 10;

if ($lessons_completed >= $total_lessons) {
    echo "You already completed all lessons! 🎉";
    exit;
}

// ✅ Increment once per completion
$update = $conn->prepare("
    UPDATE user_progress 
    SET lessons_completed = lessons_completed + 1,
        progress_percent = LEAST(ROUND(((missions_completed + lessons_completed + 1) / 20) * 100), 100),
        last_updated = NOW()
    WHERE user_id = ?
");
$update->bind_param("i", $user_id);
$update->execute();
$update->close();

echo "✅ Lesson marked as completed!";
$conn->close();
?>
