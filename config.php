<?php
// Hostinger Database Configuration

$host = "localhost";               // Usually 'localhost' on Hostinger
$db   = "u983508915_smartyplay"; // Replace with your full database name from hPanel
$user = "u983508915_root";       // Replace with your database username from hPanel
$pass = "Smartyplayground2025";        // Replace with your database password from hPanel

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
