<?php
include 'db_connect.php';
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$birthday = $_POST['birthday'] ?? null;

if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    $check->close();
    exit;
}
$check->close();

// Generate random password
function generatePassword($length = 8)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'), 0, $length);
}

$plain_password = generatePassword();
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// Generate verification token
 $verification_code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

$full_name = trim("$first_name $middle_name $last_name");

// Insert user
$stmt = $conn->prepare("INSERT INTO users (name, email, password, address, birthday, role, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, 'student', ?, 0)");
$stmt->bind_param("ssssss", $full_name, $email, $hashed_password, $address, $birthday, $verification_code);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;

    // ✅ Insert the user into user_progress
    $progress_stmt = $conn->prepare("INSERT INTO user_progress (user_id) VALUES (?)");
    $progress_stmt->bind_param("i", $user_id);
    $progress_stmt->execute();
    $progress_stmt->close();

    // ✅ Send verification email with PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mr.ocampo12@gmail.com';
        $mail->Password = 'mrpf dljl ryjv ctzz';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@smartyplayground.com', 'Smarty Playground');
        $mail->addAddress($email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Smarty Playground! Verify Your Account';

        $verify_link = "https://darkcyan-mink-932088.hostingersite.com/user_login.php";
        $mail->Body = "
            <h2>Welcome, $first_name! 🎉</h2>
            <p>Your account has been created by the administrator.</p>
            <p><b>Login Email:</b> $email<br>
               <b>Temporary Password:</b> $plain_password</p>
               <b></b></p>
               <b>Verification Code:</b> $verification_code</p>
               
            <p>Please click the link below to login your email and start verying your account after:</p>
            <a href='$verify_link' style='
                background: #10b981;
                color: white;
                padding: 10px 15px;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            '>Login Account</a>
            <br><br>
            <p style='color:#555'>If you didn’t expect this email, you can safely ignore it.</p>
        ";

        $mail->send();

        echo json_encode([
            'success' => true,
            'user_id' => $user_id,
            'name' => $full_name,
            'email' => $email,
            'message' => 'User added, progress initialized, and email sent!'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => "User added but email failed: {$mail->ErrorInfo}"
        ]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
