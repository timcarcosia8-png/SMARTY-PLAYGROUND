<?php
session_start();
include "filter_input.php";
include "db_connect.php"; // your MySQL connection

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$successMessage = "";
$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = filterInput($_POST['email']);

    if (empty($email)) {
        $errorMessages[] = "Please enter your email";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errorMessages[] = "Email doesn't exist";
        } else {
            $user = $result->fetch_assoc();
            $user_id = $user['user_id'];
            $name = $user['name'];

            // Generate 5-digit verification code
            // Generate a secure token
            // Generate a secure token
            $reset_token = bin2hex(random_bytes(16));
            
            // Update token in database
            $update = $conn->prepare("UPDATE users SET reset_token = ? WHERE user_id = ?");
            $update->bind_param("si", $reset_token, $user_id);
            $update->execute();


            // Send email
            $mail = new PHPMailer(true);
            try {
                $verificationLink = "https://darkcyan-mink-932088.hostingersite.com/reset_password.php?token=$reset_token";

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mr.ocampo12@gmail.com';
                $mail->Password = 'mrpf dljl ryjv ctzz';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('no-reply@smartyplayground.com', 'Smarty Playground');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Smarty Playground - Password Reset';
                $mail->Body = "
                    <h2>Hi $name,</h2>
                    <p>Click the link below to reset your password:</p>
                    <a href='$verificationLink'>$verificationLink</a>
                    <p>This link will expire after a certain time for security.</p>
                ";

                $mail->send();
                $successMessage = "We have sent a reset link to your email. Please check your inbox.";
            } catch (Exception $e) {
                $errorMessages[] = "Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        }
        $stmt->close();
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
        body {
            background: linear-gradient(180deg, #7C3AED 0%, #6B21A8 40%, #4C1D95 70%, #2D1B69 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite ease-in-out;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.2;
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        .title {
            font-family: 'Fredoka One', cursive;
            color: #FCD34D;
            letter-spacing: 1px;
            text-shadow:
                3px 3px 0px #F97316,
                6px 6px 0px #7C3AED,
                -1px -1px 0px rgba(255, 255, 255, 0.3);
        }

        .back-btn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        input {
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.5);
            transform: translateY(-2px);
        }

        .send-btn {
            background: linear-gradient(180deg, #FB923C 0%, #F97316 50%, #EA580C 100%);
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4);
        }

        .send-btn:active {
            transform: scale(0.95);
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.4);
        }

        .content-wrapper {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div id="stars"></div>

    <div class="relative z-10 max-w-md mx-auto min-h-screen flex flex-col px-6 pt-12">

        <!-- Back Button -->
        <div class="px-6 pt-4">
            <button onclick="history.back()"
                class="back-btn bg-white rounded-full w-12 h-12 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="#4C1D95" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Title Section -->
        <div class="content-wrapper flex-1 flex flex-col px-6 pt-12">
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

            <!-- Email Input + Send Button -->
            <form method="POST" class="mb-auto">
                <input type="email" name="email" placeholder="Enter Email Here"
                    class="w-full px-5 py-4 rounded-2xl text-black-400 text-base bg-white mb-6" required />
                <button type="submit" class="send-btn w-full py-4 rounded-full text-white text-xl font-bold">
                    Send Email
                </button>
            </form>
        </div>
    </div>

    <script>
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 80; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            const size = Math.random() * 3 + 1;
            star.style.width = size + 'px';
            star.style.height = size + 'px';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            star.style.animationDuration = (Math.random() * 2 + 2) + 's';
            starsContainer.appendChild(star);
        }
    </script>
</body>

</html>