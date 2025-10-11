<?php
session_start();
$bdservername = "localhost";
$bdusername = "root";
$bdpassword = "";
$database = "smarty_playground";

$conn = mysqli_connect($bdservername, $bdusername, $bdpassword, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
