<?php
require 'db_connect.php';
$email = 'superadmin@smartyplayground.com';
$plain = 'admin123';

$stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

if (password_verify($plain, $hash)) {
    echo "Password verified OK.\n";
} else {
    echo "Password does NOT match.\n";
}
$conn->close();
?>
