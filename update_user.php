<?php
session_start();
include "db_connect.php";
header('Content-Type: application/json');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$currentRole = $_SESSION['role'];

// ✅ Get input values safely
$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// ✅ Validate required fields
if (!$id || empty($name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// ✅ Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// ✅ Prevent admins from editing Super Admin accounts
if ($currentRole !== 'superadmin') {
    $check = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->bind_result($targetRole);
    $check->fetch();
    $check->close();

    if ($targetRole === 'superadmin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Cannot edit a Super Admin account.']);
        exit;
    }
}

// ✅ Prepare the correct query
if (!empty($password)) {
    // Hash new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $email, $hashedPassword, $id);
} else {
    // Keep existing password
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
}

// ✅ Execute and respond
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
