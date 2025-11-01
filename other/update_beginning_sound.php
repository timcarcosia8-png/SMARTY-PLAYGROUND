<?php
session_start();
include "database/db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the JSON data from POST
$data = json_decode(file_get_contents('php://input'), true);

// Handle final score separately
if (isset($data['final_score'])) {
    $final_score = intval($data['final_score']);
    $total_questions = intval($data['total_questions']);

    // Optional: update a 'total_score' column in user_progress
    // Example: uncomment if you have a column 'beginning_sounds_score' in user_progress
    /*
    $stmt = $conn->prepare("UPDATE user_progress SET beginning_sounds_score = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $final_score, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    */

    echo json_encode(['success' => true]);
    exit;
}

// Extract individual question data
$user_id = $_SESSION['user_id'];
$question_id = intval($data['question_id'] ?? 0);
$chosen_option = $data['chosen_option'] ?? null;
$correct_letter = $data['correct_letter'] ?? null;

// Validate required fields
if (!isset($user_id, $question_id, $chosen_option, $correct_letter)) {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
    exit;
}

// Determine if the answer is correct
$is_correct = ($chosen_option === $correct_letter);

// Check if a progress row already exists
$stmt = $conn->prepare("SELECT id, attempts FROM user_beginning_sounds_progress WHERE user_id = ? AND question_id = ?");
$stmt->bind_param("ii", $user_id, $question_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $attempts);

if ($stmt->num_rows > 0) {
    $stmt->fetch();
    $attempts++;
    // Update existing record
    $updateStmt = $conn->prepare("
        UPDATE user_beginning_sounds_progress 
        SET chosen_option=?, is_correct=?, attempts=?, last_played=NOW() 
        WHERE id=?
    ");
    $updateStmt->bind_param("siii", $chosen_option, $is_correct, $attempts, $id);
    $updateStmt->execute();
    $updateStmt->close();
} else {
    $attempts = 1;
    // Insert new record
    $insertStmt = $conn->prepare("
        INSERT INTO user_beginning_sounds_progress 
        (user_id, question_id, chosen_option, is_correct, attempts, last_played) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $insertStmt->bind_param("iisii", $user_id, $question_id, $chosen_option, $is_correct, $attempts);
    $insertStmt->execute();
    $insertStmt->close();
}

$stmt->close();
$conn->close();

// Return success
echo json_encode(['success' => true, 'is_correct' => $is_correct]);
