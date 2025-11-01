<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT user_id, verification_code, avatar FROM users WHERE email = ? AND is_verified = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['verification_code'] === $code) {
            // Mark verified
            $update = $conn->prepare("UPDATE users SET is_verified = 1, status = 'active', verification_code = NULL WHERE user_id = ?");
            $update->bind_param("i", $row['user_id']);
            $update->execute();

            echo "<script>
                alert('Email verified successfully!');
                window.location.href = 'user_login.php?user_id={$row['user_id']}';
            </script>";
        } else {
            echo "<script>alert('Incorrect verification code. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Email not found or already verified.'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smarty Playground - Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            font-family: 'Arial Black', sans-serif;
            font-weight: 900;
            letter-spacing: 2px;
            text-shadow:
                3px 3px 0px #F97316,
                6px 6px 0px #7C3AED,
                -1px -1px 0px rgba(255, 255, 255, 0.3);
            animation: titlePulse 2s ease-in-out infinite;
        }

        @keyframes titlePulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .back-btn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        .code-input {
            width: 60px;
            height: 70px;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
            caret-color: #7C3AED;
        }

        .code-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.5);
            transform: scale(1.05);
        }

        .apply-btn {
            background: linear-gradient(180deg, #22D3EE 0%, #06B6D4 50%, #0891B2 100%);
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
        }

        .apply-btn:active {
            transform: scale(0.95);
            box-shadow: 0 4px 10px rgba(6, 182, 212, 0.4);
        }

        .resend-btn {
            background: linear-gradient(180deg, #FB923C 0%, #F97316 50%, #EA580C 100%);
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(249, 115, 22, 0.4);
        }

        .resend-btn:active {
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
    <!-- Stars Background -->
    <div id="stars"></div>

    <!-- Main Container -->
    <div class="relative z-10 max-w-md mx-auto min-h-screen flex flex-col">
        <!-- Back Button -->
        <div class="px-6 pt-4">
            <button onclick="history.back()" class="back-btn bg-white rounded-full w-12 h-12 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="#4C1D95" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <!-- Content Wrapper -->
        <div class="content-wrapper flex-1 flex flex-col px-6 pt-8">
            <!-- Title Section -->
            <div class="text-center mb-12">
                <h1 class="title text-4xl leading-tight mb-6" style="color: #FCD34D;">
                    SMARTY<br>PLAYGROUND
                </h1>
                <h2 class="text-white text-2xl font-bold mb-3">Enter Verification Code</h2>
                <p class="text-white text-sm opacity-90 leading-relaxed">
                    Please check your email, we've sent a 4-digit code.
                </p>
            </div>

            <!-- Verification Form -->
            <form method="POST" class="flex flex-col items-center gap-6">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

                <!-- Code Input Section -->
                <div class="flex justify-center gap-3 mb-8">
                    <input type="text" maxlength="1" class="code-input rounded-2xl bg-white text-purple-900" id="code1"
                        oninput="moveToNext(this, 'code2')" onkeydown="moveToPrev(event, this, null)" />
                    <input type="text" maxlength="1" class="code-input rounded-2xl bg-white text-purple-900" id="code2"
                        oninput="moveToNext(this, 'code3')" onkeydown="moveToPrev(event, this, 'code1')" />
                    <input type="text" maxlength="1" class="code-input rounded-2xl bg-white text-purple-900" id="code3"
                        oninput="moveToNext(this, 'code4')" onkeydown="moveToPrev(event, this, 'code2')" />
                    <input type="text" maxlength="1" class="code-input rounded-2xl bg-white text-purple-900" id="code4"
                        oninput="moveToNext(this, null)" onkeydown="moveToPrev(event, this, 'code3')" />
                </div>

                <input type="hidden" name="code" id="fullCode">

                <!-- Buttons Section -->
                <div class="space-y-4 w-full">
                    <button type="submit" class="apply-btn w-full py-4 rounded-full text-white text-xl font-bold">
                        Apply Code
                    </button>
                    <button type="button" onclick="resendEmail()" class="resend-btn w-full py-4 rounded-full text-white text-xl font-bold">
                        Send Email Again
                    </button>
                </div>
            </form>
        </div>

        <div class="h-12"></div>
    </div>

    <script>
        // Create stars
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

        // Combine code inputs into one hidden field before submit
        document.querySelector('form').addEventListener('submit', () => {
            const code = ['code1', 'code2', 'code3', 'code4'].map(id => document.getElementById(id).value).join('');
            document.getElementById('fullCode').value = code;
        });

        // Auto-focus next input
        function moveToNext(current, nextId) {
            if (current.value.length === 1 && nextId) {
                document.getElementById(nextId).focus();
            }
        }

        // Handle backspace to move to previous input
        function moveToPrev(event, current, prevId) {
            if (event.key === 'Backspace' && current.value === '' && prevId) {
                document.getElementById(prevId).focus();
            }
        }

        // Auto-focus first input on load
        window.addEventListener('load', () => {
            document.getElementById('code1').focus();
        });

        // Placeholder resend function
        function resendEmail() {
            alert("Resend email functionality coming soon!");
        }
    </script>
</body>

</html>
