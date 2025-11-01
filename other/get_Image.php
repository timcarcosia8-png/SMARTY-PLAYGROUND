<?php
header('Content-Type: application/json');
include "filter_input.php";
include "database/db_connect.php";

// Fetch all beginning sounds
$sql = "SELECT id, word, image_path, sound_letter FROM beginning_sounds";
$result = $conn->query($sql);

$questions = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['id'];

        // Fetch options for this question
        $opt_sql = "SELECT option_letter FROM beginning_sounds_options WHERE question_id = $questionId";
        $opt_result = $conn->query($opt_sql);

        $options = [];
        if ($opt_result->num_rows > 0) {
            while ($opt_row = $opt_result->fetch_assoc()) {
                $options[] = $opt_row['option_letter'];
            }
        }

        $questions[] = [
            'image' => $row['image_path'],     // path to image
            'sound' => $row['sound_letter'],   // correct letter
            'word' => $row['word'],            // word
            'options' => $options               // multiple choice letters
        ];
    }
}

echo json_encode($questions);
