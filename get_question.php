<?php
header('Content-Type: application/json');

include "filter_input.php";
include "database/db_connect.php";

$questions = [];

$sql = "SELECT * FROM game4_questions";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'image' => '/SMARTY-PLAYGROUND/game/image/' . basename($row['image']), // relative path
            'correct' => $row['correct'],
            'wrong' => $row['wrong']
        ];
    }
}

echo json_encode($questions);
$conn->close();
?>
