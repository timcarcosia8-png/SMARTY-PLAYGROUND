<?php
include "db_connect.php";

$id = $_GET['id'] ?? 0;
$response = ["status" => "error", "message" => "Invalid request."];

if ($id) {
    $query = "SELECT file_path FROM videos WHERE id = $id";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $filePath = $row['file_path'];
        // Delete file from server
        if (file_exists($filePath)) unlink($filePath);
    }

    if ($conn->query("DELETE FROM videos WHERE id = $id")) {
        $response = ["status" => "success", "message" => "Video deleted successfully!"];
    } else {
        $response = ["status" => "error", "message" => "Failed to delete video."];
    }
}

echo json_encode($response);
?>
