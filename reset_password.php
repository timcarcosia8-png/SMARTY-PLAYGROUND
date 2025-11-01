<?php
session_start();
include "db_connect.php";

if(!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

// Check token in DB
$stmt = $conn->prepare("SELECT user_id FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    die("Invalid or expired token.");
}

$user = $result->fetch_assoc();
$user_id = $user['user_id'];

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];

    if($password !== $confirm){
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE user_id = ?");
        $stmt->bind_param("si", $hashed, $user_id);
        $stmt->execute();
        $success = "Password has been reset successfully!";
        header("Location: login.php?reset=success");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smarty Playground - Reset Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(180deg, #7C3AED 0%, #6B21A8 40%, #4C1D95 70%, #2D1B69 100%);
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
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
    .sparkle {
      position: absolute;
      animation: sparkle 2s infinite;
      font-size: 20px;
    }
    @keyframes sparkle {
      0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
      50% { opacity: 1; transform: scale(1) rotate(180deg); }
    }
    .title {
      font-family: 'Fredoka One', cursive;
      color: #FCD34D;
      letter-spacing: 1px;
      text-shadow: 
        3px 3px 0px #F97316,
        6px 6px 0px #7C3AED,
        -1px -1px 0px rgba(255,255,255,0.3);
    }
    input {
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
    }
    input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }
    input::placeholder {
      color: #9CA3AF;
    }
    .reset-btn {
      background: linear-gradient(180deg, #22D3EE 0%, #06B6D4 50%, #0891B2 100%);
      color: white;
      transition: all 0.3s ease;
      font-weight: 600;
      box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
    }
    .reset-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(6, 182, 212, 0.5);
    }
    .reset-btn:active {
      transform: scale(0.95);
    }
    .back-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
  </style>
</head>

<body>
  <div id="stars"></div>

  <!-- Sparkles -->
  <div class="sparkle" style="top: 15%; left: 20%; animation-delay: 0s;">✨</div>
  <div class="sparkle" style="top: 25%; right: 15%; animation-delay: 1s;">✨</div>
  <div class="sparkle" style="bottom: 40%; left: 10%; animation-delay: 2s;">⭐</div>
  <div class="sparkle" style="top: 20%; right: 25%; animation-delay: 1.5s;">⭐</div>

  <div class="relative z-10 max-w-md mx-auto min-h-screen flex flex-col px-6 py-4">
    <!-- Back Button -->
    <div class="px-6 pt-4">
      <button onclick="history.back()" class="back-btn bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transition-transform">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="#4C1D95" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <!-- Title Section -->
    <div class="flex-1 flex flex-col px-8 pt-12">
      <div class="text-center mb-8">
        <h1 class="title text-4xl leading-tight mb-3" style="color: #FCD34D;">
          SMARTY<br>PLAYGROUND
        </h1>
        <h2 class="text-white text-2xl font-semibold mb-2">Reset Password</h2>
        <p class="text-white text-sm opacity-90 px-2">Please enter your new password.</p>
      </div>

      <!-- Form Section -->
      <form method="POST" class="space-y-4 mb-6">
        <?php if(isset($error)) echo "<p class='text-red-400 text-center'>$error</p>"; ?>
        <input type="password" name="password" placeholder="Enter New Password" class="w-full px-5 py-4 rounded-xl text-gray-700 text-base bg-white" required>
        <input type="password" name="confirmPassword" placeholder="Confirm Password" class="w-full px-5 py-4 rounded-xl text-gray-700 text-base bg-white" required>
        <button type="submit" class="reset-btn w-full py-4 rounded-full text-lg font-semibold">Reset Password</button>
      </form>
    </div>
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
