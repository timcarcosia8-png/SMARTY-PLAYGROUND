<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Robust input handling: accept form-encoded POST or raw JSON body
$lesson_id = 0;
if (isset($_POST['lesson_id'])) {
    $lesson_id = intval($_POST['lesson_id']);
} else {
    // try alternate keys
    if (isset($_POST['id'])) $lesson_id = intval($_POST['id']);
}

// if still missing, try JSON payload
if ($lesson_id <= 0) {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            if (!empty($json['lesson_id'])) $lesson_id = intval($json['lesson_id']);
            elseif (!empty($json['id'])) $lesson_id = intval($json['id']);
        }
    }
}

// final validation
if ($lesson_id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid lesson ID.',
        'debug' => [
            'POST_keys' => array_keys($_POST),
            'raw_input_present' => !empty($raw ?? '')
        ]
    ]);
    exit;
}

// ensure lesson exists in videos table
$chkVideo = $conn->prepare("SELECT video_id FROM videos WHERE video_id = ?");
$chkVideo->bind_param("i", $lesson_id);
$chkVideo->execute();
$chkVideo->store_result();
if ($chkVideo->num_rows === 0) {
    $chkVideo->close();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Lesson not found.']);
    exit;
}
$chkVideo->close();

// get total lessons
$totalRes = $conn->query("SELECT COUNT(*) AS total FROM videos");
$total_lessons = 0;
if ($totalRes && $r = $totalRes->fetch_assoc()) $total_lessons = intval($r['total']);
if ($total_lessons === 0) {
    echo json_encode(['success' => false, 'message' => 'No lessons available.']);
    exit;
}

// Prevent double counting: check user_lesson table
$checkStmt = $conn->prepare("SELECT 1 FROM user_lessons_completed WHERE user_id = ? AND video_id = ?");
$checkStmt->bind_param("ii", $user_id, $lesson_id);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    echo json_encode(['success' => false, 'message' => 'Lesson already completed.']);
    exit;
}
$checkStmt->close();

// Wrap insertion + progress update in transaction
$conn->begin_transaction();

try {
    // Insert record to mark this specific lesson completed
    $ins = $conn->prepare("INSERT INTO user_lessons_completed (user_id, video_id, completed_at) VALUES (?, ?, NOW())");
    $ins->bind_param("ii", $user_id, $lesson_id);
    if (!$ins->execute()) throw new Exception('Failed to insert completion: ' . $ins->error);
    $ins->close();

    // If user_progress row doesn't exist create it (safe-guard)
    $ap = $conn->prepare("SELECT user_id FROM user_progress WHERE user_id = ?");
    $ap->bind_param("i", $user_id);
    $ap->execute();
    $ap->store_result();
    if ($ap->num_rows === 0) {
        $ap->close();
        $create = $conn->prepare("INSERT INTO user_progress (user_id, missions_completed, lessons_completed, last_updated) VALUES (?, 0, 0, NOW())");
        $create->bind_param("i", $user_id);
        if (!$create->execute()) throw new Exception('Failed to create user_progress: ' . $create->error);
        $create->close();
    } else {
        $ap->close();
    }

    // increment lessons_completed (only once because we checked earlier)
    $upd = $conn->prepare("UPDATE user_progress SET lessons_completed = lessons_completed + 1, last_updated = NOW() WHERE user_id = ?");
    $upd->bind_param("i", $user_id);
    if (!$upd->execute()) throw new Exception('Failed to update user_progress: ' . $upd->error);
    $upd->close();

    // Recalculate progress_percent — simple approach: percent = lessons_completed / total_lessons * 100 (capped)
    $calc = $conn->prepare("SELECT lessons_completed, missions_completed FROM user_progress WHERE user_id = ?");
    $calc->bind_param("i", $user_id);
    $calc->execute();
    $calc->bind_result($lessons_completed_after, $missions_completed_after);
    $calc->fetch();
    $calc->close();

    // Avoid division by zero (we already have total_lessons > 0)
    $percent = round(($lessons_completed_after / $total_lessons) * 100);
    if ($percent > 100) $percent = 100;
    if ($percent < 0) $percent = 0;

    $upd2 = $conn->prepare("UPDATE user_progress SET progress_percent = ? WHERE user_id = ?");
    $upd2->bind_param("ii", $percent, $user_id);
    if (!$upd2->execute()) throw new Exception('Failed to update progress_percent: ' . $upd2->error);
    $upd2->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => "✅ Lesson marked as completed!",
        'data' => [
            'lesson_id' => $lesson_id,
            'lessons_completed' => $lessons_completed_after,
            'total_lessons' => $total_lessons,
            'progress_percent' => $percent
        ]
    ]);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
    exit;
}
?>
