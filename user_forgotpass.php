<?php
session_start();
include "filter_input.php";
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$successMessage = "";
$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = filterInput($_POST['email']);
    $xml = new DOMDocument();
    $xml->load("../xml_database/users.xml");
    $users = $xml->getElementsByTagName("user");

    if (empty($email)) {
        $errorMessages[] = "Please enter your email";
    } else {
        $emailFound = false;
        $reset_token = bin2hex(random_bytes(16));

        foreach ($users as $user) {
            if ($email == $user->getElementsByTagName("email")->item(0)->nodeValue) {
                $emailFound = true;

                // Save token
                $resetNode = $user->getElementsByTagName("resetToken")->item(0);
                if ($resetNode) {
                    $resetNode->nodeValue = $reset_token;
                } else {
                    $user->appendChild($xml->createElement("resetToken", $reset_token));
                }
                $xml->save("../xml_database/users.xml");

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $verificationLink = "https://pixelgearxpress.infy.uk/login_registration/reset_password_form.php?token=$reset_token";

                    $mail->isSMTP();
                    $mail->Host = 'smtp-relay.brevo.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '8de232001@smtp-brevo.com'; // SMTP user
                    $mail->Password = 'xsmtpsib-655a7bfaf74a5579db059f2a193e4eda9edd1dd0ed030bbfad0d6016874de53c-YBPmhDnX84MgS7LO'; // SMTP password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('mr.ocampo12@gmail.com', 'PixelGearXpress');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password Request';
                    $mail->Body = "Hi,<br><br>If you wish to reset your password, please click the link below:<br><br>
                    <a href='$verificationLink'>$verificationLink</a><br><br>Cheers,<br>PixelGearXpress Team";

                    $mail->send();
                    $successMessage = "We have sent a reset link to your email. You may now close this tab and reset your password.";
                } catch (Exception $e) {
                    $errorMessages[] = "Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                break;
            }
        }

        if (!$emailFound) {
            $errorMessages[] = "Email doesn't exist";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smarty Playground - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* keep all your original Tailwind + custom styles from the second code */
    </style>
</head>
<body>
    <div id="stars"></div>
    <div class="relative z-10 max-w-md mx-auto min-h-screen flex flex-col px-6 pt-12">

        <!-- Back Button -->
        <div class="pb-6">
            <button class="back-btn bg-white rounded-full w-12 h-12 flex items-center justify-center" onclick="history.back()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="#4C1D95" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <!-- Title Section -->
        <div class="text-center mb-8">
            <h1 class="title text-4xl leading-tight mb-3">SMARTY<br>PLAYGROUND</h1>
            <h2 class="text-white text-2xl font-bold mb-3">Forgot Password</h2>
            <p class="text-white text-sm opacity-90 leading-relaxed">
                Enter your email here, we'll send email<br>to reset your password
            </p>
        </div>

        <!-- Messages -->
        <?php if ($successMessage): ?>
            <p class="text-green-400 text-center mb-4"><?= $successMessage ?></p>
        <?php endif; ?>
        <?php if (!empty($errorMessages)): ?>
            <?php foreach ($errorMessages as $error): ?>
                <p class="text-red-400 text-center mb-2"><?= $error ?></p>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Email Input Section -->
        <form method="POST" class="mb-auto">
            <input 
                type="email" 
                name="email"
                placeholder="Enter Email Here" 
                class="w-full px-5 py-4 rounded-2xl text-gray-400 text-base bg-white mb-6"
                required
            />
            <button type="submit" class="send-btn w-full py-4 rounded-full text-white text-xl font-bold">
                Send Email
            </button>
        </form>

    </div>

    <script>
        // create stars animation (keep original script from second code)
    </script>
</body>
</html>
