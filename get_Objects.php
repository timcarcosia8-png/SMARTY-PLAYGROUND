<?php

include 'database/db_connect.php';
$sql = "SELECT id, name FROM objects_audio";
$result = $conn->query($sql);

$words = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $words[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

echo json_encode($words);
$conn->close();

?>
