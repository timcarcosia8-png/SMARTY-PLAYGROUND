<?php
include "db_connect.php";

// Fetch all letters and audio paths
$sql = "SELECT letter, audio FROM letter_audio";
$result = $conn->query($sql);

$letters = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $letters[$row['letter']] = $row['audio'];
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($letters);
