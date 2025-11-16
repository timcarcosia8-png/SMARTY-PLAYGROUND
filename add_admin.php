<?php
session_start();
include "db_connect.php";
header('Content-Type: application/json');

// ✅ 1. BACKEND ACCESS CONTROL — Only Super Admin can access this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    // Log unauthorized access attempts (optional but recommended)
    error_log("❌ Unauthorized add_admin attempt by user_id: " . ($_SESSION['user_id'] ?? 'guest'));

    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied. Super Admins only."]);
    exit;
}

// ✅ 2. INPUT VALIDATION
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? '');

// Check for empty fields
if (empty($name) || empty($email) || empty($password) || empty($role)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit;
}

// Enforce minimum password length
if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long."]);
    exit;
}

// ✅ 3. ROLE VALIDATION — only admin or superadmin allowed
$allowed_roles = ['admin', 'superadmin'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode(["success" => false, "message" => "Invalid role."]);
    exit;
}

// ✅ 4. Prevent normal admins from creating another superadmin (defense-in-depth)
if ($role === 'superadmin' && $_SESSION['role'] !== 'superadmin') {
    echo json_encode(["success" => false, "message" => "Only a Super Admin can create another Super Admin."]);
    exit;
}

// ✅ 5. CHECK IF EMAIL ALREADY EXISTS
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    $stmt->close();
    exit;
}
$stmt->close();

// ✅ 6. INSERT NEW ADMIN OR SUPERADMIN
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, is_verified, status)
    VALUES (?, ?, ?, ?, 1, 'active')
");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => ucfirst($role) . " added successfully.",
        "role" => $role,
        "user_id" => $stmt->insert_id
    ]);
} else {
    error_log("⚠️ Failed to insert user: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Error adding user. Please try again."]);
}

$stmt->close();
$conn->close();
?>
