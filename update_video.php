<?php
include "db_connect.php";

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];

// Optional: handle video upload
if (!empty($_FILES['video']['name'])) {
    $targetDir = "uploads/";
    $videoName = basename($_FILES['video']['name']);
    $targetFile = $targetDir . time() . "_" . $videoName;
    move_uploaded_file($_FILES['video']['tmp_name'], $targetFile);

    $query = "UPDATE videos SET title=?, description=?, file_path=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $title, $description, $targetFile, $id);
} else {
    $query = "UPDATE videos SET title=?, description=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $description, $id);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Video updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update video."]);
}
?>
