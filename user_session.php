<?php
session_start();

// If student is not logged in, redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: user_login.php"); // adjust to your actual student login page
    exit();
}

// If the logged-in user is NOT a student, redirect them away
if ($_SESSION['role'] !== 'student') {
    header("Location: admin_login.php"); // redirect others (like admin) to their dashboard
    exit();
}

// Optional: store reusable session data
$username = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
?>
