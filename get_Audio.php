<?php

include "database/db_connect.php";

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing audio ID"]);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT audio FROM objects_audio WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($audio);
$stmt->fetch();

if ($audio) {
    $audioBase64 = base64_encode($audio);
    $audioSrc = "data:audio/mpeg;base64," . $audioBase64;
    echo json_encode(["audio" => $audioSrc]);
} else {
    echo json_encode(["error" => "Audio not found"]);
}

$stmt->close();
$conn->close();
?>
