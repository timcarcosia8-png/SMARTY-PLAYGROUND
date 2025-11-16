<?php
session_start();

// Check if the student is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: user_login.php");
    exit();
}

// Check if the logged-in user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: admin_login.php"); // redirect if it's an admin
    exit();
}

// Optional: Reusable session variables
$user_id = $_SESSION['user_id'];
$username = $_SESSION['name'];
$is_verified = $_SESSION['is_verified'];
$avatar = $_SESSION['avatar'];
?>
