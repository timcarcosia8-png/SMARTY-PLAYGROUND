<?php
include "filter_input.php";
include "db_connect.php";

$result = $conn->query("SELECT id, key_name, label, file_path, image_path FROM game_sounds");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'key' => $row['key_name'],
        'label' => $row['label'],
        'audio' => $row['file_path'],
        'image' => $row['image_path']

    ];

}

header('Content-Type: application/json');
echo json_encode($data);
?>