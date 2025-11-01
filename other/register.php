<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'user_tab/others/phpmailer/Exception.php';
require 'user_tab/others/phpmailer/PHPMailer.php';
require 'user_tab/others/phpmailer/SMTP.php';
include 'database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.history.back();</script>";
        exit;
    }

    $check_email->close();

    // Generate 4-digit verification code
    $verification_code = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, verification_code, is_verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $verification_code);

    if ($stmt->execute()) {
        // Send verification email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'mr.ocampo12@gmail.com'; // Your Gmail
            $mail->Password = 'mrpf dljl ryjv ctzz';   // Use App Password (not your real password)
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('no-reply@smartyplayground.com', 'Smarty Playground');
            $mail->addAddress($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Smarty Playground - Email Verification';
            $mail->Body = "
                <h2>Welcome to Smarty Playground!</h2>
                <p>Your 4-digit verification code is:</p>
                <h1 style='color:#7C3AED;'>$verification_code</h1>
                <p>Please enter this code to verify your email address.</p>
            ";

            $mail->send();

            echo "<script>alert('Registration successful! Please check your email for verification code.'); window.location.href='verify.php?email=$email';</script>";

        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
