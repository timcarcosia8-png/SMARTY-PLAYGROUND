<?php 
session_start();
$username = "";
$usertype = "";
if (!isset($_SESSION['username'])) {
    header("Location: admin page/admin_login.php");
    exit();
} else {
    $username = $_SESSION['username'];
    $usertype = $_SESSION['usertype'];
    if ($usertype !== "admin") {
        header("Location: ../users/users_dashboard.php");
        exit();
    }
}