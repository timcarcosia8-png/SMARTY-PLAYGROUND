<?php
session_start();
include "db_connect.php";
header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$loggedInRole = $_SESSION['role'];
$loggedInId = $_SESSION['user_id'];

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing user ID']);
    exit;
}

// ✅ Get the role of the user being deleted
$stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($targetRole);
$stmt->fetch();
$stmt->close();

if (!$targetRole) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// ✅ Restriction rules
if ($targetRole === 'superadmin') {
    echo json_encode(['success' => false, 'message' => 'Cannot delete a Super Admin']);
    exit;
}

if ($loggedInRole === 'admin' && $targetRole !== 'student' && $targetRole !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Admins can only delete students or teachers']);
    exit;
}

// ✅ Prevent deleting own account accidentally
if ($loggedInId == $id) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

// ✅ Proceed with deletion
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
