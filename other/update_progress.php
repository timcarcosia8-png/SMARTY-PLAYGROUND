<?php
session_start();
include '../database/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$points = $data['points'];
$level = $data['level'];
$missions_completed = $data['missionsCompleted'];
$lessons_completed = $data['lessonsCompleted'];
$progress_percent = $data['progressPercent'];

$stmt = $conn->prepare("
    INSERT INTO user_progress (user_id, points, level, missions_completed, lessons_completed, progress_percent)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        points = VALUES(points),
        level = VALUES(level),
        missions_completed = VALUES(missions_completed),
        lessons_completed = VALUES(lessons_completed),
        progress_percent = VALUES(progress_percent),
        last_updated = CURRENT_TIMESTAMP
");
$stmt->bind_param("iiiiii", $user_id, $points, $level, $missions_completed, $lessons_completed, $progress_percent);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
