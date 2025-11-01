<?php
if (session_status() == PHP_SESSION_NONE) session_start();

$dbservername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "smarty_playground";

$conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
