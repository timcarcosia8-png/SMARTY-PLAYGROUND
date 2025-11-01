<?php
$dbservername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "smarty_playground";

$conn = new mysqli($dbservername, $dbusername, $dbpassword, $database);

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Audio file
$audioData = file_get_contents("sounds/kevin-dog.mp3"); // read binary data
$name = "dog";

$stmt = $conn->prepare("INSERT INTO objects_audio (name, audio) VALUES (?, ?)");
$stmt->bind_param("sb", $name, $null); 
$stmt->send_long_data(1, $audioData);

if ($stmt->execute()) echo "Audio inserted!";
else echo "Error: " . $stmt->error;

$stmt->close();
$conn->close();
?>
