<?php
include "db_connect.php";

$id = $_GET['id'] ?? 0;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

if ($id && $title) {
    $stmt = $conn->prepare("UPDATE videos SET title=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $description, $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Video updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update video."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input."]);
}
?>
