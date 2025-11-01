<?php
include "db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!empty($_FILES['video']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES['video']['name']);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['mp4', 'mov', 'avi', 'mkv'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO videos (title, description, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $description, $targetFilePath);
                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Video uploaded successfully"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Database insert failed"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "File upload failed"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid file type"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No video file uploaded"]);
    }
}
?>
