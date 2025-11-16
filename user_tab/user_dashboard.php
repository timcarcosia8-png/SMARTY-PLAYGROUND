<?php
session_start();
include 'db_connect.php';
include "user_session.php";
// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, avatar, is_verified FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $avatar, $is_verified);
$stmt->fetch();
$stmt->close();

// If not verified, go to verify page
if ($is_verified == 0) {
    header("Location: verify.php");
    exit;
}

// If no avatar, go to avatar select
if (empty($avatar)) {
    header("Location: user_cAvatar.php");
    exit;
}


$completedLessons = [];
$progressQuery = $conn->prepare("SELECT video_id FROM user_lessons_completed WHERE user_id = ?");
$progressQuery->bind_param("i", $user_id);
$progressQuery->execute();
$progressResult = $progressQuery->get_result();
while ($row = $progressResult->fetch_assoc()) {
    $completedLessons[] = $row['video_id'];
}
$progressQuery->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SMARTY PLAYGROUND</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade {
            animation: fadeInScale 0.4s ease-out;
        }

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

        .avatar {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card:active {
            transform: translateY(-2px);
        }

        .lesson-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .lesson-item:hover {
            background: #f9fafb;
            transform: translateX(5px);
        }

        .lesson-item:active {
            transform: scale(0.98);
        }

        .nav-btn {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-btn:active {
            transform: scale(0.9);
        }

        .nav-btn.active {
            color: #667eea;
        }

        .floating-card {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .diamond-badge {
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
        }

        .daily-practice-card {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .play-icon {
            font-size: 1.2rem;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="phone-container">
        <div id="stars"></div>

        <!-- HEADER -->
        <header class="px-5 pt-6 pb-4 relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img id="userAvatar" class="avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" />
                    <div>
                        <p class="text-white text-xs opacity-80 font-medium">Welcome back</p>
                        <p class="text-white font-bold text-xl" id="userName"><?php echo htmlspecialchars($name); ?></p>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- MAIN CONTENT -->
        <main class="px-5 pb-24 relative z-10">
            <!-- Top Cards -->
            <div class="grid grid-cols-3 gap-3 mb-5">
                <div class="card bg-cyan-400 rounded-2xl p-4 flex flex-col items-center justify-center h-28 shadow-lg"
                    onclick="window.location.href='user_game3.php'">
                    <span class="text-3xl mb-2">📚</span>
                    <span class="text-white text-xs font-semibold text-center leading-tight">Sounds<br>Arround Us</span>
                </div>

                <div class="card bg-emerald-400 rounded-2xl p-4 flex flex-col items-center justify-center h-28 shadow-lg"
                    onclick="window.location.href='user_game2.php'">
                    <span class="text-3xl mb-2">⏱️</span>
                    <span class="text-white text-xs font-semibold text-center leading-tight">Beginning<br>Sounds</span>
                </div>

                <div class="card bg-orange-400 rounded-2xl p-4 flex flex-col items-center justify-center h-28 shadow-lg"
                    onclick="window.location.href='user_game4.php'">
                    <span class="text-3xl mb-2">🎁</span>
                    <span class="text-white text-xs font-semibold text-center leading-tight">Medial<br>/a/<br>Sound</span>
                </div>
            </div>

            <!-- Daily Practice -->
            <section class="daily-practice-card rounded-3xl p-6 mb-5 shadow-xl relative overflow-hidden floating-card">
                <h2 class="text-white text-2xl font-bold mb-3">Spell the Word</h2>
                <p class="text-white/90 text-sm mb-4">Drag letters to spell words!</p>
                <button
                    class="bg-white text-orange-500 font-bold px-8 py-3 rounded-full text-sm shadow-lg hover:shadow-xl transition-all hover:scale-105 active:scale-95"
                    onclick="window.location.href='user_game1.php'">
                    Start Quiz ✨
                </button>

                <div class="absolute right-4 bottom-4 flex gap-2">
                    <div
                        class="bg-white/20 backdrop-blur-sm rounded-2xl w-12 h-12 flex items-center justify-center transform rotate-12">
                        <span class="text-white text-2xl font-bold">A</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-2xl w-12 h-12 flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">B</span>
                    </div>
                    <div
                        class="bg-white/20 backdrop-blur-sm rounded-2xl w-12 h-12 flex items-center justify-center transform -rotate-12">
                        <span class="text-white text-2xl font-bold">C</span>
                    </div>
                </div>
            </section>

            <!-- Lessons -->
            <section class="bg-white rounded-3xl p-5 shadow-xl relative">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-800 font-bold text-xl">Reading Lessons</h3>
                    <span class="text-2xl">🎯</span>
                </div>

                <div class="space-y-3">
                    <?php
                    $videoQuery = $conn->query("SELECT * FROM videos ORDER BY uploaded_at DESC");
                    $index = 1;

                    if ($videoQuery && $videoQuery->num_rows > 0) {
                        while ($video = $videoQuery->fetch_assoc()) {
                            $color = ['cyan', 'emerald', 'orange', 'purple', 'teal', 'pink'][($index - 1) % 6];
                            $title = htmlspecialchars($video['title']);
                            $desc = htmlspecialchars($video['description'] ?? 'No description');
                            $path = htmlspecialchars($video['file_path']);

                            echo "
                    <div class='lesson-item flex items-center justify-between p-4 rounded-xl border-2 border-gray-100'>
                        <div class='flex items-center gap-4'>
                            <div class='w-12 h-12 bg-gradient-to-br from-{$color}-400 to-{$color}-500 rounded-xl flex items-center justify-center shadow-md'>
                                <span class='text-white text-xl font-bold'>{$index}</span>
                            </div>
                            <div>
                                <p class='text-gray-800 font-bold text-sm'>{$title}</p>
                                <p class='text-gray-500 text-xs mb-1'>{$desc}</p>
                            </div>
                        </div>
                    <button onclick=\"openVideoModal('{$path}', '{$title}', {$video['video_id']})\" class='text-{$color}-400 text-xl hover:scale-110 transition'>▶️</button>
                    

                    </div>
                    ";
                            $index++;
                        }
                    } else {
                        echo "<p class='text-gray-500 text-center'>No videos available.</p>";
                    }
                    ?>
                </div>

                <!-- 🎬 Video Modal -->
                <div id="videoModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden relative w-11/12 md:w-2/3 lg:w-1/2">
                        <button onclick="closeVideoModal()"
                            class="absolute top-3 right-3 bg-gray-800 text-white rounded-full px-3 py-1 hover:bg-gray-600 transition">✕</button>

                        <div class="p-4 border-b">
                            <h2 id="videoTitle" class="text-lg font-bold text-gray-800"></h2>
                        </div>

                        <video id="lessonVideo" class="w-full rounded-b-2xl" controls>
                            <source src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        
                        <div class="p-4 text-center">
                          <button id="doneButton"
                              class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-6 py-2 rounded-full transition">
                              ✅ Done Watching
                            </button>

                        </div>

                    </div>
                </div>
            </section>

            
        </main>

        <!-- FOOTER -->
        <footer class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[400px] bg-white rounded-t-3xl shadow-2xl px-6 py-4 z-20">
      <div class="flex justify-around items-center relative">
        <button class="nav-btn active flex flex-col items-center gap-1">
          <span class="text-3xl">🏠</span>
          <span class="text-xs">Home</span>
        </button>
        
        <button class="nav-btn flex flex-col items-center gap-1 text-gray-400" onclick="window.location.href='user_progress.php'">
          <span class="text-3xl">📊</span>
          <span class="text-xs">Progress</span>
        </button>
        
        <button class="nav-btn flex flex-col items-center gap-1 text-gray-400" onclick="window.location.href='user_profile.php'">
          <span class="text-3xl">👤</span>
          <span class="text-xs">Profile</span>
        </button>
      </div>
    </footer>
    </div>

    <script>
        
           
                        
                function markLessonComplete(lessonId) {
                  const doneBtn = document.querySelector(`#doneButton[data-lesson="${lessonId}"]`);
                
                  if (sessionStorage.getItem("completed_" + lessonId)) {
                    alert("You already completed this lesson!");
                    return;
                  }
                
                  const xhr = new XMLHttpRequest();
                  xhr.open("POST", "updated_lessons_completed.php", true);
                  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                  xhr.onload = function () {
                    if (xhr.status === 200) {
                      alert(xhr.responseText);
                
                      // ✅ Disable only this lesson's button
                      doneBtn.disabled = true;
                      doneBtn.textContent = "Completed ✅";
                      doneBtn.classList.add("bg-gray-400", "cursor-not-allowed");
                      doneBtn.classList.remove("bg-teal-500", "hover:bg-teal-600");
                
                      // Save locally
                      sessionStorage.setItem("completed_" + lessonId, true);
                
                      closeVideoModal();
                    } else {
                      alert("Error updating progress!");
                    }
                  };
                  xhr.send("lesson_id=" + encodeURIComponent(lessonId));
                }
                
                function openVideoModal(videoSrc, title, lessonId) {
                  const modal = document.getElementById('videoModal');
                  const video = document.getElementById('lessonVideo');
                  const titleEl = document.getElementById('videoTitle');
                  const doneBtn = document.getElementById('doneButton');
                
                  // Assign the lesson-specific ID
                  doneBtn.setAttribute("data-lesson", lessonId);
                
                  // Update button state
                  if (sessionStorage.getItem("completed_" + lessonId)) {
                    doneBtn.disabled = true;
                    doneBtn.textContent = "Completed ✅";
                    doneBtn.classList.add("bg-gray-400", "cursor-not-allowed");
                    doneBtn.classList.remove("bg-teal-500", "hover:bg-teal-600");
                  } else {
                    doneBtn.disabled = false;
                    doneBtn.textContent = "✅ Done Watching";
                    doneBtn.classList.add("bg-teal-500", "hover:bg-teal-600");
                    doneBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
                  }
                
                  modal.classList.remove('hidden');
                  titleEl.textContent = title;
                  video.src = videoSrc;
                  video.play();
                
                  // Attach the correct action dynamically
                  doneBtn.onclick = () => markLessonComplete(lessonId);
                }

                
                function closeVideoModal() {
                  const modal = document.getElementById('videoModal');
                  const video = document.getElementById('lessonVideo');
                  video.pause();
                  modal.classList.add('hidden');
                  video.src = "";
                }

            
        
        const starsContainer = document.getElementById('stars');
            for (let i = 0; i < 30; i++) {
              const star = document.createElement('div');
              star.className = 'star';
              const size = Math.random() * 3 + 1;
              star.style.width = size + 'px';
              star.style.height = size + 'px';
              star.style.left = Math.random() * 100 + '%';
              star.style.top = Math.random() * 60 + '%';
              star.style.animationDelay = Math.random() * 3 + 's';
              star.style.animationDuration = (Math.random() * 2 + 2) + 's';
              starsContainer.appendChild(star);
            }
            
            // ✅ Move this OUTSIDE the loop
            let bgMusic;
            
            function initBackgroundMusic() {
              bgMusic = new Audio('bg_dashboard.mp3');
              bgMusic.loop = true;
              bgMusic.volume = 0.005;
            }
            
            function fadeInMusic(targetVolume = 0.005, step = 0.001, interval = 150) {
              let vol = 0;
              bgMusic.volume = vol;
              const fade = setInterval(() => {
                if (vol < targetVolume) {
                  vol += step;
                  bgMusic.volume = Math.min(vol, targetVolume);
                } else {
                  clearInterval(fade);
                }
              }, interval);
            }
            
            function tryPlayMusic() {
              if (!bgMusic) initBackgroundMusic();
              bgMusic.play().then(() => fadeInMusic()).catch(() => {
                document.body.addEventListener('click', () => {
                  bgMusic.play().then(() => fadeInMusic());
                }, { once: true });
              });
            }
            
            function toggleMusic() {
              if (!bgMusic) initBackgroundMusic();
              if (bgMusic.paused) bgMusic.play();
              else bgMusic.pause();
            }
            
            window.addEventListener('DOMContentLoaded', tryPlayMusic);

    </script>
</body>

</html>