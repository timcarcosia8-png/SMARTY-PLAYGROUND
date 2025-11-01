<?php
session_start();
include '../database/db_connect.php';

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
    header("Location: verify_email.php");
    exit;
}

// If no avatar, go to avatar select
if (empty($avatar)) {
    header("Location: user_cAvatar.php");
    exit;
}
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
                    <span class="text-white text-xs font-semibold text-center leading-tight">Guess<br>What</span>
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
            <section class="bg-white rounded-3xl p-5 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-800 font-bold text-xl">Reading Lessons</h3>
                    <span class="text-2xl">🎯</span>
                </div>

                <div class="space-y-3">
                    <?php
                    $lessons = [
                        ["Sounds Around Us", "Learn Different Sounds Around Us", "cyan"],
                        ["Beginning Alphabet Sounds", "Solve 10 Math Questions", "emerald"],
                        ["Ending Alphabet Sounds", "Solve 15 Math Questions", "orange"],
                        ["Medial Short /a/ Sound", "Solve 20 Math Questions", "purple"]
                    ];
                    $index = 1;
                    foreach ($lessons as $lesson) {
                        echo "
            <div class='lesson-item flex items-center justify-between p-4 rounded-xl border-2 border-gray-100'>
              <div class='flex items-center gap-4'>
                <div class='w-12 h-12 bg-gradient-to-br from-{$lesson[2]}-400 to-{$lesson[2]}-500 rounded-xl flex items-center justify-center shadow-md'>
                  <span class='text-white text-xl font-bold'>{$index}</span>
                </div>
                <div>
                  <p class='text-gray-800 font-bold text-sm'>{$lesson[0]}</p>
                  <p class='text-gray-500 text-xs mb-1'>{$lesson[1]}</p>
                </div>
              </div>
              <div class='play-icon text-{$lesson[2]}-400'>▶️</div>
            </div>";
                        $index++;
                    }
                    ?>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer
            class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[400px] bg-white rounded-t-3xl shadow-2xl px-6 py-4 z-20">
            <div class="flex justify-around items-center relative">
                <button class="nav-btn active flex flex-col items-center gap-1">
                    <span class="text-3xl">🏠</span>
                    <span class="text-xs font-semibold">Home</span>
                </button>
                <button class="nav-btn flex flex-col items-center gap-1 text-gray-400"
                    onclick="window.location.href='user_progress.php'">
                    <span class="text-3xl">📊</span>
                    <span class="text-xs">Progress</span>
                </button>
                <button class="nav-btn flex flex-col items-center gap-1 text-gray-400"
                    onclick="window.location.href='user_profile.php'">
                    <span class="text-3xl">👤</span>
                    <span class="text-xs">Profile</span>
                </button>
            </div>
        </footer>
    </div>

    <script>
        // Background stars
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
    </script>
</body>

</html>