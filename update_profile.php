<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized access.";
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');

// ✅ Validate
if (empty($name)) {
    echo "Please enter your name.";
    exit;
}

// ✅ Update only name
$stmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
$stmt->bind_param("si", $name, $user_id);

if ($stmt->execute()) {
    echo "Profile name updated successfully!";
} else {
    echo "Error updating name: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
