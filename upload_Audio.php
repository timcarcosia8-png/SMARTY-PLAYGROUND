<?php
include 'database/db_connect.php';

if (isset($_POST['name']) && isset($_FILES['audio'])) {
    $name = $_POST['name'];
    $audioData = file_get_contents($_FILES['audio']['tmp_name']);

    $stmt = $conn->prepare("INSERT INTO objects_audio (name, audio) VALUES (?, ?)");
    $null = NULL;
    $stmt->bind_param("sb", $name, $null);
    $stmt->send_long_data(1, $audioData);

    if ($stmt->execute()) {
        echo "Audio uploaded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No file or name provided.";
}

$conn->close();
?>
