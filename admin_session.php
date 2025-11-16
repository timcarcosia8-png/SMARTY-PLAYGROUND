<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: admin_login.php");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// Redirect if not admin/superadmin
if ($role !== 'admin' && $role !== 'superadmin') {
    header("Location: home.php");
    exit();
}

// Optional: restrict certain actions to Super Admin only
$isSuperAdmin = ($role === 'superadmin');
?>
