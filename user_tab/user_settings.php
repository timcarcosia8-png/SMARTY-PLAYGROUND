<?php
include "user_session.php"; // ✅ Handles session & role validation
include "filter_input.php";
include "db_connect.php";

// Fetch user info from database
$stmt = $conn->prepare("SELECT name, avatar, points FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $avatar, $points);
$stmt->fetch();
$stmt->close();

// Default avatar if none
if (empty($avatar)) {
    $avatar = "Hero1.png";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings - SMARTY PLAYGROUND</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Fredoka', sans-serif;
    }

    .phone-container {
      max-width: 400px;
      margin: 0 auto;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      position: relative;
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

    .avatar-large {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid white;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .back-btn {
      transition: all 0.3s ease;
    }

    .back-btn:active {
      transform: scale(0.9);
    }

    .settings-btn {
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .settings-btn:active {
      transform: scale(0.98);
    }

    .logout-btn {
      transition: all 0.3s ease;
    }

    .logout-btn:active {
      transform: scale(0.98);
    }

    .app-title {
      background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
      font-weight: 800;
      letter-spacing: 1px;
    }
  </style>
</head>

<body class="bg-gray-100">
  <div class="phone-container">
    <!-- Background Stars -->
    <div id="stars"></div>

    <!-- HEADER -->
    <header class="px-5 pt-6 pb-4 relative z-10 flex justify-between items-center">
      <button onclick="history.back()" class="back-btn w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="#667eea" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <h1 class="text-white font-bold text-2xl">Settings</h1>
      <div class="w-12"></div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="px-5 pb-8 relative z-10">
      
      <!-- Profile Section -->
      <section class="bg-white rounded-3xl p-6 shadow-xl mb-5">
        <div class="flex flex-col items-center">
          <!-- Avatar -->
          <img id="userAvatar" class="avatar-large mb-3" src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" />
          
          <!-- Name -->
          <h2 class="text-gray-800 font-bold text-xl mb-1" id="userName"><?php echo htmlspecialchars($name); ?></h2>
          <p class="text-gray-500 text-sm">Level 5 Champion</p>
        </div>
      </section>

      <!-- Settings Options -->
      <section class="space-y-3 mb-5">
        <!-- Edit Profile -->
        <button onclick="openModal('editProfileModal')" 
          class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Edit Profile</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <!-- Change Password -->
        <button onclick="openModal('changePasswordModal')" 
          class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Change Password</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <!-- Notifications -->
        <button class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Notifications</span>
          <div class="flex items-center gap-2">
            <span class="text-gray-400 text-sm">On</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </button>

        <!-- Sound Effects -->
        <button class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Sound Effects</span>
          <div class="flex items-center gap-2">
            <span class="text-gray-400 text-sm">On</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </button>

        <!-- Privacy Policy -->
        <button class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Privacy Policy</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <!-- Help & Support -->
        <button class="settings-btn w-full bg-white rounded-2xl p-4 shadow-lg flex items-center justify-between">
          <span class="text-gray-800 font-semibold text-base">Help & Support</span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M9 18L15 12L9 6" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </section>

      <!-- Logout Button -->
      <button onclick="logout()" class="logout-btn w-full bg-gradient-to-r from-orange-400 to-red-500 rounded-2xl p-4 shadow-xl flex items-center justify-center mb-5">
        <span class="text-white font-bold text-base mr-2">Logout</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <!-- App Branding -->
      <div class="text-center mb-4">
        <h2 class="app-title text-4xl mb-2">SMARTY</h2>
        <h2 class="app-title text-4xl">PLAYGROUND</h2>
        <p class="text-white/60 text-xs mt-3">Version 1.0.0</p>
      </div>
    </main>
  </div>

  <!-- Logout Confirmation Modal -->
  <div id="logoutModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
      <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
        <span class="text-5xl">👋</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-800 mb-2">Logout?</h3>
      <p class="text-gray-600 mb-6">Are you sure you want to logout?</p>
      <div class="flex gap-3">
        <button onclick="closeModal('logoutModal')" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
          Cancel
        </button>
        <button onclick="confirmLogout()" class="flex-1 bg-gradient-to-r from-orange-400 to-red-500 text-white py-3 rounded-2xl font-bold shadow-lg">
          Logout
        </button>
      </div>
    </div>
  </div>
  
<!-- ✏️ EDIT PROFILE MODAL -->
   <!-- ✏️ EDIT PROFILE MODAL -->
<div id="editProfileModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
    <h3 class="text-2xl font-bold text-gray-800 mb-2">Edit Profile</h3>
    <p class="text-gray-600 mb-6">Update your display name or avatar below.</p>

    <form id="editProfileForm">
      <!-- Name -->
      <div class="mb-4 text-left">
        <label class="block text-gray-700 font-medium mb-1">Name</label>
        <input type="text" id="editName" name="name" value="<?php echo htmlspecialchars($name); ?>"
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <!-- Avatar -->
      <!--<div class="mb-6 text-left">-->
      <!--  <label class="block text-gray-700 font-medium mb-1">Avatar</label>-->
      <!--  <input type="file" id="editAvatar" name="avatar" accept="image/*"-->
      <!--    class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">-->
      <!--</div>-->

      <div class="flex gap-3">
        <button type="button" onclick="closeModal('editProfileModal')" 
          class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
          Cancel
        </button>
        <button type="submit"
          class="flex-1 bg-gradient-to-r from-blue-400 to-blue-600 text-white py-3 rounded-2xl font-bold shadow-lg">
          Save
        </button>
      </div>
    </form>
  </div>
</div>

<!-- 🔐 CHANGE PASSWORD MODAL -->
<div id="changePasswordModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-3xl p-8 max-w-sm w-11/12 mx-4 text-center">
    <h3 class="text-2xl font-bold text-gray-800 mb-2">Change Password</h3>
    <p class="text-gray-600 mb-6">Enter your current and new password below.</p>

    <form id="changePasswordForm">
      <div class="mb-3 text-left">
        <label class="block text-gray-700 font-medium mb-1">Current Password</label>
        <input type="password" id="currentPassword" name="currentPassword" 
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div class="mb-3 text-left">
        <label class="block text-gray-700 font-medium mb-1">New Password</label>
        <input type="password" id="newPassword" name="newPassword" 
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div class="mb-6 text-left">
        <label class="block text-gray-700 font-medium mb-1">Confirm New Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" 
          class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div class="flex gap-3">
        <button type="button" onclick="closeModal('changePasswordModal')" 
          class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-2xl font-bold">
          Cancel
        </button>
        <button type="submit"
          class="flex-1 bg-gradient-to-r from-blue-400 to-blue-600 text-white py-3 rounded-2xl font-bold shadow-lg">
          Save
        </button>
      </div>
    </form>
  </div>
</div>



  <script>
    // Create background stars
    const starsContainer = document.getElementById('stars');
    for (let i = 0; i < 30; i++) {
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
    
    
        // ✅ Show modal
        function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
        else console.warn('Modal not found:', id);
      }
    
      // ✅ Close modal
      function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
        else console.warn('Modal not found:', id);
      }
    
      // ✅ Edit Profile AJAX
      document.getElementById('editProfileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('update_profile.php', { method: 'POST', body: formData });
        const result = await res.text();
        alert(result);
        closeModal('editProfileModal');
      });

    
      // ✅ Change Password AJAX
      document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('change_password.php', { method: 'POST', body: formData });
        const result = await res.text();
        alert(result);
        closeModal('changePasswordModal');
      });
      
      
      document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await fetch('change_password.php', { method: 'POST', body: formData });
        const result = await res.text();
        alert(result);
        closeModal('logoutModal');
      });
    
      // ✅ Logout modal controls
      function logout() {
        openModal('logoutModal');
      }
    
      function confirmLogout() {
        // Keep user progress
        const progressKeys = [
          'wordGameCompleted', 'readingGameCompleted',
          'lesson1Completed', 'lesson2Completed', 'lesson3Completed', 'lesson4Completed',
          'math1Completed', 'math2Completed', 'math3Completed',
          'totalPoints', 'lessonsCompleted', 'gamesWon', 'dailyStreak',
          'trophiesEarned', 'goalsCompleted', 'gemsCollected'
        ];
    
        const progressData = {};
        progressKeys.forEach(key => {
          const value = localStorage.getItem(key);
          if (value) progressData[key] = value;
        });
    
        localStorage.clear();
        Object.keys(progressData).forEach(key => {
          localStorage.setItem(key, progressData[key]);
        });
    
        alert('Logged out successfully!');
        window.location.href = 'user_logout.php';
      }
  </script>
</body>
</html>