<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, name, password, is_verified, avatar FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $name, $hashed_password, $is_verified, $avatar);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['is_verified'] = $is_verified;
            $_SESSION['avatar'] = $avatar;

            // Redirect logic
            if ($is_verified == 0) {
                // Not yet verified → verify email
                header("Location: verify_email.php");
                exit;
            } elseif ($is_verified == 1 && empty($avatar)) {
                // Verified but no avatar yet → choose avatar
                header("Location: user_cAvatar.php");
                exit;
            } else {
                // Verified and has avatar → dashboard
                header("Location: user_dashboard.php");
                exit;
            }
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email!');</script>";
    }
    $stmt->close();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smarty Playground - Login</title>
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
            0%, 100% { opacity: 0.2; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        .title {
            font-family: 'Arial Black', sans-serif;
            font-weight: 900;
            letter-spacing: 2px;
            text-shadow:
                3px 3px 0px #F97316,
                6px 6px 0px #7C3AED,
                -1px -1px 0px rgba(255,255,255,0.3);
            animation: titlePulse 2s ease-in-out infinite;
        }
        @keyframes titlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .back-btn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .back-btn:active { transform: scale(0.9); }
        input {
            transition: all 0.3s ease;
        }
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.5);
            transform: translateY(-2px);
        }
        .login-btn {
            background: linear-gradient(180deg, #22D3EE 0%, #06B6D4 50%, #0891B2 100%);
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 1px;
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
        }
        .login-btn:active {
            transform: scale(0.95);
            box-shadow: 0 4px 10px rgba(6, 182, 212, 0.4);
        }
        .link-text {
            color: #22D3EE;
            text-decoration: underline;
            cursor: pointer;
            transition: color 0.2s;
        }
        .link-text:hover { color: #67E8F9; }
        .forgot-link {
            color: #22D3EE;
            cursor: pointer;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #67E8F9; }
        .content-wrapper { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
            <button class="back-btn bg-white rounded-full w-12 h-12 flex items-center justify-center"
                onclick="window.location.href='home.php'">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="#4C1D95" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper flex-1 flex flex-col px-6 pt-8">
            <div class="text-center mb-8">
                <h1 class="title text-4xl leading-tight mb-4" style="color: #FCD34D;">
                    SMARTY<br>PLAYGROUND
                </h1>
                <h2 class="text-white text-2xl font-bold mb-2">Log In Your Account</h2>
                <p class="text-white text-sm opacity-90">Start your journey in smarty playground fun way</p>
            </div>
            
            <!-- Form Section -->
            <form method="POST" class="space-y-4 mb-2">
                <input 
                    type="email" 
                    name="email"
                    placeholder="Enter Email Here" 
                    required
                    class="w-full px-5 py-4 rounded-2xl text-gray-400 text-base bg-white"
                />
                <input 
                    type="password" 
                    name="password"
                    placeholder="Enter Password Here" 
                    required
                    class="w-full px-5 py-4 rounded-2xl text-gray-400 text-base bg-white"
                />

                <!-- Forgot Password -->
                <div class="text-right mb-2">
                    <a href="#" class="forgot-link text-sm font-semibold">Forgot Password?</a>
                </div>

                <!-- Terms -->
                <p class="text-white text-xs text-center leading-relaxed">
                    By continuing, you confirm that you are 6 years or older and agree to our 
                    <span class="link-text">Terms & Conditions</span> and 
                    <span class="link-text">Privacy Policy</span>.
                </p>

                <!-- Login Button -->
                <button type="submit" class="login-btn w-full py-4 rounded-full text-white text-xl font-bold">
                    Login
                </button>
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
    </script>
</body>
</html>
