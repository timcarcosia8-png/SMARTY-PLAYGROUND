<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

$user_id = $_SESSION['user_id'];

$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// ✅ Validate input
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo "All fields are required.";
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo "New passwords do not match.";
    exit;
}

if (strlen($newPassword) < 6) {
    echo "Password must be at least 6 characters long.";
    exit;
}

// ✅ Fetch current password hash
$stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($dbPassword);
$stmt->fetch();
$stmt->close();

// ✅ Verify old password
if (!password_verify($currentPassword, $dbPassword)) {
    echo "Current password is incorrect.";
    exit;
}

// ✅ Hash new password
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

// ✅ Update in DB
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $newHash, $user_id);

if ($stmt->execute()) {
    echo "Password changed successfully!";
} else {
    echo "Error updating password: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
