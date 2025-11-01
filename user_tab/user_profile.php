<?php
session_start();
include '../database/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, avatar, points, is_verified FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $avatar, $points, $is_verified);
$stmt->fetch();
$stmt->close();

// Default avatar if none
if (empty($avatar)) {
  $avatar = "../user_tab/Hero1.png";
}

// Progress logic
$maxPoints = 1000;
$progressPercent = min(100, ($points / $maxPoints) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      overflow-x: hidden;
    }
    .star { position: fixed; width: 3px; height: 3px; background: white; border-radius: 50%; animation: twinkle 2s infinite; }
    @keyframes twinkle { 0%, 100% { opacity: 0.3; } 50% { opacity: 1; } }

    .profile-container { max-width: 400px; margin: 0 auto; padding: 20px; padding-bottom: 100px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header-title { color: white; font-size: 1.8rem; font-weight: 700; }
    .settings-btn { background: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); transition: all 0.3s ease; }
    .settings-btn:hover { transform: rotate(90deg) scale(1.1); }

    .profile-card { background: white; border-radius: 30px; padding: 30px; text-align: center; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
    .avatar-container { width: 120px; height: 120px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 8px 20px rgba(34, 211, 238, 0.4); position: relative; overflow: hidden; }
    .avatar-container img { width: 100%; height: 100%; object-fit: cover; }

    .camera-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
      width: 35px; height: 35px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      border: 3px solid white;
      cursor: pointer;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    .camera-badge:hover { transform: scale(1.1); }

    .student-name { color: #374151; font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; }
    .progress-container { background: #f3f4f6; border-radius: 20px; padding: 15px; margin-top: 15px; }
    .progress-label { color: #6b7280; font-size: 0.875rem; font-weight: 600; margin-bottom: 8px; text-align: left; }
    .progress-bar-bg { background: #e5e7eb; border-radius: 50px; height: 12px; overflow: hidden; position: relative; }
    .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #22d3ee 0%, #06b6d4 100%); border-radius: 50px; transition: width 0.5s ease; position: relative; overflow: hidden; }
    .progress-bar-fill::after {
      content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      animation: shimmer 2s infinite;
    }
    @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .progress-text { color: #374151; font-size: 0.875rem; font-weight: 600; margin-top: 8px; text-align: right; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
    .stat-card { background: white; border-radius: 20px; padding: 20px 15px; text-align: center; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; cursor: pointer; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
    .stat-icon { font-size: 2rem; margin-bottom: 8px; }
    .stat-label { color: #6b7280; font-size: 0.875rem; font-weight: 600; margin-bottom: 5px; }
    .stat-value { color: #374151; font-size: 1.5rem; font-weight: 700; }

    .badges-section { background: white; border-radius: 25px; padding: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); }
    .badges-title { font-size: 1.3rem; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .badges-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    .badge { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 20px; animation: float 3s ease-in-out infinite; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
    .badge:hover { transform: scale(1.15) rotate(10deg); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15); }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

    .bottom-nav { position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); background: white; border-radius: 30px 30px 0 0; padding: 15px 20px; box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.2); display: flex; justify-content: space-around; align-items: center; width: 100%; max-width: 400px; z-index: 100; }
    .nav-item { display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: all 0.3s ease; color: #9ca3af; padding: 5px 10px; }
    .nav-item:hover { transform: translateY(-3px); }
    .nav-item.active { color: #667eea; }
    .nav-icon { font-size: 1.8rem; margin-bottom: 5px; }
    .nav-label { font-size: 0.7rem; font-weight: 600; }
  </style>
</head>
<body>
  <div id="stars"></div>

  <div class="profile-container">
    <!-- Header -->
    <div class="header">
      <div class="header-title">My Profile</div>
      <div class="settings-btn" onclick="alert('Settings coming soon!')">
        <svg width="24" height="24" fill="none" stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="3"></circle>
          <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m6.36 0l4.24-4.24M5.64 18.36l4.24-4.24m6.36 0l4.24 4.24"></path>
        </svg>
      </div>
    </div>

    <!-- Profile Card -->
    <div class="profile-card">
      <div class="avatar-container">
        <img id="profileAvatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Student Avatar">
        <!-- <div class="camera-badge" onclick="alert('Avatar change coming soon!')">📷</div> -->
      </div>
      <div class="student-name"><?php echo htmlspecialchars($name); ?></div>

      <div class="progress-container">
        <div class="progress-label">Progress to Next Level</div>
        <div class="progress-bar-bg">
          <div class="progress-bar-fill" style="width: <?php echo $progressPercent; ?>%;"></div>
        </div>
        <div class="progress-text">
          <span><?php echo $points; ?></span> / <span><?php echo $maxPoints; ?></span> Points
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card"><div class="stat-icon">🏆</div><div class="stat-label">Rank</div><div class="stat-value">102</div></div>
      <div class="stat-card"><div class="stat-icon">🎯</div><div class="stat-label">Level</div><div class="stat-value">7</div></div>
      <div class="stat-card"><div class="stat-icon">💎</div><div class="stat-label">Coins</div><div class="stat-value"><?php echo $points; ?></div></div>
    </div>

    <!-- Badges -->
    <div class="badges-section">
      <div class="badges-title"><span>🏅</span><span>Achievements</span></div>
      <div class="badges-grid">
        <div class="badge" title="First Steps">🌟</div>
        <div class="badge" title="Word Master">📚</div>
        <div class="badge" title="Math Whiz">🔢</div>
        <div class="badge" title="Speed Reader">⚡</div>
        <div class="badge" title="Perfect Score">💯</div>
        <div class="badge" title="Daily Streak">🔥</div>
        <div class="badge" title="Team Player">🤝</div>
        <div class="badge" title="Champion">👑</div>
      </div>
    </div>
  </div>

  <!-- Bottom Nav -->
  <div class="bottom-nav">
    <div class="nav-item" onclick="window.location.href='user_dashboard.php'"><div class="nav-icon">🏠</div><div class="nav-label">Home</div></div>
    <div class="nav-item" onclick="window.location.href='user_progress.php'"><div class="nav-icon">📊</div><div class="nav-label">Progress</div></div>
    <div class="nav-item active"><div class="nav-icon">👤</div><div class="nav-label">Profile</div></div>
  </div>

  <script>
    // Stars background
    const starsContainer = document.getElementById('stars');
    for (let i = 0; i < 50; i++) {
      const star = document.createElement('div');
      star.className = 'star';
      star.style.left = Math.random() * 100 + '%';
      star.style.top = Math.random() * 100 + '%';
      star.style.animationDelay = Math.random() * 2 + 's';
      starsContainer.appendChild(star);
    }
  </script>
</body>
</html>
