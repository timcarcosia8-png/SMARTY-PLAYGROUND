<?php
include 'database/db_connect.php';

if (!isset($_GET['letter'])) {
    http_response_code(400);
    echo "Missing letter parameter.";
    exit;
}

$letter = strtoupper($_GET['letter']);

$stmt = $conn->prepare("SELECT audio FROM letter_audio WHERE letter = ?");
$stmt->bind_param("s", $letter);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($audio);
    $stmt->fetch();

    // Output audio headers
    header("Content-Type: audio/mpeg");
    header("Content-Length: " . strlen($audio));
    header("Cache-Control: public, max-age=3600");
    header("Accept-Ranges: bytes");

    echo $audio;
} else {
    http_response_code(404);
    echo "Audio not found for letter " . htmlspecialchars($letter);
}

$stmt->close();
$conn->close();
?>
