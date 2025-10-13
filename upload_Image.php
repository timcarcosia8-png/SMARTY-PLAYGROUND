<?php
include "../database/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $word = $_POST['word'];
    $sound = $_POST['sound_letter'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageType = $_FILES['image']['type'];

        $stmt = $conn->prepare("INSERT INTO beginning_sounds (word, image, image_type, sound_letter) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sbss", $word, $null, $imageType, $sound);

        // Send the blob data separately
        $stmt->send_long_data(1, $imageData);
        if ($stmt->execute()) {
            echo "Question added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No image uploaded or upload error!";
    }
}

$conn->close();
?>
