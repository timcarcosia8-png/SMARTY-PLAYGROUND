<?php
header('Content-Type: application/json');

include "filter_input.php";
include "db_connect.php";

$questions = [];

$sql = "SELECT * FROM game4_questions";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'image' => $row['image'],           // relative path or emoji
            'correct' => $row['correct'],       // correct word
            'wrong' => $row['wrong'],           // wrong word
            'sound' => $row['sound'],           // phonetic breakdown
            'correctAudio' => $row['correctAudio'], // path to correct audio
            'wrongAudio' => $row['wrongAudio']      // path to wrong audio
        ];
    }
}

echo json_encode($questions);
$conn->close();
?>
