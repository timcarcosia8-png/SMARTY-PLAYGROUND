<?php
include "../filter_input.php";
include "../database/db_connect.php";
// include "../get_Objects.php";
// include "../get_Audio.php";



?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Sounds Around Us — Matching Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .game-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 30px;
            max-width: 500px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .title {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .score-box {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 20px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }

        .score-label {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .score-value {
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        .main-btn {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            border: none;
            width: 100%;
            margin-bottom: 15px;
            text-transform: uppercase;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .main-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }

        .main-btn:active {
            transform: scale(0.95);
        }

        .secondary-btn {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        .secondary-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
        }

        .secondary-btn:active {
            transform: translateY(0);
        }

        .reset-btn {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        }

        .reset-btn:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
        }

        .message-box {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #5b21b6;
            margin-bottom: 20px;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 4px solid transparent;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
            border-color: #8b5cf6;
        }

        .card:active {
            transform: scale(0.95);
        }

        .card-image {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
        }

        .card-label {
            text-align: center;
            font-size: 1.2rem;
            font-weight: 700;
            color: #4b5563;
        }

        .card.correct {
            border-color: #10b981;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            animation: success 0.5s ease;
        }

        .card.wrong {
            animation: shake 0.5s ease;
        }

        .card.dimmed {
            opacity: 0.4;
        }

        @keyframes success {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1) rotate(3deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-8px);
            }

            75% {
                transform: translateX(8px);
            }
        }

        .star {
            position: fixed;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            animation: twinkle 2s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

        .celebration {
            position: fixed;
            font-size: 3rem;
            pointer-events: none;
            animation: celebrate 1s ease-out;
        }

        @keyframes celebrate {
            0% {
                transform: translateY(0) scale(0);
                opacity: 1;
            }

            100% {
                transform: translateY(-100px) scale(1.5);
                opacity: 0;
            }
        }

        .footer-tip {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 15px;
            font-size: 0.85rem;
            color: #92400e;
            font-weight: 600;
            margin-top: 20px;
        }

        .footer-tip code {
            background: #fbbf24;
            color: white;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 700;
        }
    </style>
</head>

<body class="flex flex-col items-center justify-center min-h-screen p-4">
    <!-- Background Stars -->
    <div id="stars"></div>

    <div class="game-container">
        <h1 class="title">🔊 Sounds Around Us</h1>
        <p class="subtitle">Play the sound — tap the picture that matches it!</p>

        <div class="score-box">
            <div class="score-label">Your Score</div>
            <div id="score" class="score-value">0 / 0</div>
        </div>

        <button id="playSoundBtn" class="main-btn">
            ▶️ PLAY SOUND
        </button>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button id="hintBtn" class="secondary-btn" style="flex: 1;">
                💡 HINT
            </button>
            <button id="resetBtn" class="secondary-btn reset-btn" style="flex: 1;">
                🔄 RESET
            </button>
        </div>

        <div id="message" class="message-box"></div>

        <section class="cards-grid" id="cardsGrid">

        </section>


    </div>

    <script>
        let bgMusic;
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 50; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 2 + 's';
            starsContainer.appendChild(star);
        }

        // -----------------
        // Element references
        // -----------------
        const playBtn = document.getElementById('playSoundBtn');
        const hintBtn = document.getElementById('hintBtn');
        const resetBtn = document.getElementById('resetBtn');
        const messageEl = document.getElementById('message');
        const scoreEl = document.getElementById('score');
        const grid = document.getElementById('cardsGrid');

        // -----------------
        // Game state
        // -----------------
        let sounds = [];
        let remainingSounds = [];
        let currentTarget = null;
        let totalTries = 0;
        let correctCount = 0;
        let hasPlayed = false;

        // -----------------
        // Utility functions
        // -----------------
        function updateScore() {
            scoreEl.textContent = `${correctCount} / ${totalTries}`;
        }

        function celebrate() {
            const emojis = ['🎉', '⭐', '🌟', '✨', '🎊', '🏆'];
            for (let i = 0; i < 6; i++) {
                setTimeout(() => {
                    const celebration = document.createElement('div');
                    celebration.className = 'celebration';
                    celebration.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                    celebration.style.left = Math.random() * window.innerWidth + 'px';
                    celebration.style.top = Math.random() * window.innerHeight + 'px';
                    document.body.appendChild(celebration);
                    setTimeout(() => celebration.remove(), 1000);
                }, i * 100);
            }
        }

        function chooseRandomSound() {
            if (remainingSounds.length === 0) return null;
            const idx = Math.floor(Math.random() * remainingSounds.length);
            return remainingSounds.splice(idx, 1)[0];
        }

        let currentAudio = null;
        function playCurrentSound() {
            if (!currentTarget) {
                messageEl.textContent = 'Press "PLAY SOUND" to start! 🎵';
                return;
            }
            hasPlayed = true;
            if (currentAudio) currentAudio.pause();
            currentAudio = new Audio(currentTarget.file);
            currentAudio.play().catch(() => {
                messageEl.innerHTML = `⚠️ Audio file not found: <code>${currentTarget.file}</code>`;
            });
            messageEl.textContent = '🎧 Listen carefully and pick the matching picture!';
        }


        function pickNewTargetAndPlay() {
            currentTarget = chooseRandomSound();
            hasPlayed = false;
            hintLevel = 0; // reset hint

            if (!currentTarget) {
                messageEl.textContent = '🎉 You matched all the sounds! Redirecting...';
                setTimeout(() => {
                    localStorage.setItem("readingGameCompleted", "true");
                    window.location.href = "Users/dashboard.html";
                }, 2000);
                return;
            }

            messageEl.textContent = '🎵 New sound coming up...';
            grid.querySelectorAll('.card').forEach(c => {
                c.classList.remove('correct', 'dimmed');
            });

            setTimeout(playCurrentSound, 500);
        }


        function generateCardsFromDB(sounds) {
            grid.innerHTML = ''; // clear previous cards
            sounds.forEach(sound => {
                const card = document.createElement('button');
                card.className = 'card';
                card.dataset.key = sound.key;
                card.innerHTML = `
            <div class="card-image">
                <img src="${sound.image}" alt="${sound.label}" style="max-height:90px;object-fit:contain" />
            </div>
            <div class="card-label">${sound.label}</div>
        `;

                card.addEventListener('click', () => {
                    const choice = card.dataset.key;

                    if (!currentTarget) {
                        messageEl.textContent = '⚠️ Press "PLAY SOUND" first!';
                        card.classList.add('wrong');
                        setTimeout(() => card.classList.remove('wrong'), 300);
                        return;
                    }
                    if (!hasPlayed) {
                        messageEl.textContent = '⚠️ Listen to the sound first!';
                        card.classList.add('wrong');
                        setTimeout(() => card.classList.remove('wrong'), 300);
                        return;
                    }

                    // totalTries++;  <-- remove this, wrong answers won't count
                    // Play audio for feedback
                    const clickAudio = new Audio(`/${currentTarget.file}`);
                    clickAudio.play().catch(() => console.warn('Audio file missing:', currentTarget.file));

                    if (choice === currentTarget.key) {
                        correctCount++;
                        messageEl.textContent = `✨ Excellent! That was ${currentTarget.label}! ✨`;
                        card.classList.add('correct');
                        grid.querySelectorAll('.card').forEach(c => {
                            if (c !== card) c.classList.add('dimmed');
                        });
                        celebrate();
                        totalTries++; // Only count correct answers
                        updateScore();
                        setTimeout(pickNewTargetAndPlay, 1500);
                    } else {
                        messageEl.textContent = `❌ Oops! The correct answer was "${currentTarget.label}". Moving on...`;
                        card.classList.add('wrong');
                        setTimeout(() => card.classList.remove('wrong'), 300);

                        // Move to next target immediately
                        totalTries++; // You can increment here if you want "attempts" to show
                        updateScore();
                        setTimeout(pickNewTargetAndPlay, 1500);
                    }
                });


                grid.appendChild(card);
            });
        }


        // -----------------
        // Load sounds from DB
        // -----------------
        async function loadSoundsFromDB() {
            try {
                const res = await fetch('../get_game_data.php'); // relative to current folder
                if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                const data = await res.json();

                sounds = data.map(d => ({
                    key: d.key,
                    label: d.label,
                    file: d.audio,   // "game/dog.wav"
                    image: d.image   // "game/dog.png"
                }));


                remainingSounds = [...sounds];
                generateCardsFromDB(sounds);
                updateScore();
                messageEl.textContent = '👋 Welcome! Press "PLAY SOUND" to begin the fun! 🎵';
            } catch (err) {
                console.error('Error fetching sounds:', err);
                messageEl.textContent = '❌ Error loading sounds. Check console.';
            }
        }



        hintBtn.addEventListener('click', () => {
            if (!currentTarget) return;

            hintLevel++; // increment hint each time button is clicked
            const label = currentTarget.label;

            if (hintLevel >= label.length) {
                // All letters revealed
                messageEl.textContent = `💡 Hint: It's "${label}"!`;
            } else {
                // Show first N letters
                const hint = label.slice(0, hintLevel);
                messageEl.textContent = `💡 Hint: Starts with "${hint}"`;
            }
        });







        // -----------------
        // Event listeners
        // -----------------
        playBtn.addEventListener('click', () => {
            if (!currentTarget) {
                pickNewTargetAndPlay();
                return;
            }

            if (currentAudio) currentAudio.pause();
            currentAudio = new Audio(currentTarget.file); // relative path, e.g., "game/image/dog.wav"

            currentAudio.play().catch(err => {
                console.error('Audio playback blocked or missing:', currentTarget.file, err);
                messageEl.textContent = `⚠️ Audio file missing or not supported: ${currentTarget.file}`;
            });

            hasPlayed = true;
            messageEl.textContent = '🎧 Listen carefully and pick the matching picture!';
        });

        resetBtn.addEventListener('click', () => {
            totalTries = 0;
            correctCount = 0;
            currentTarget = null;
            hasPlayed = false;
            remainingSounds = [...sounds];
            messageEl.textContent = '🔄 Game reset! Press "PLAY SOUND" to start fresh! 🎮';
            updateScore();
            cards.forEach(c => {
                c.classList.remove('correct', 'dimmed', 'wrong');
            });
        });

        function initBackgroundMusic() {
            bgMusic = new Audio('/SMARTY-PLAYGROUND/game/sounds/bg_game3.mp3');
            bgMusic.loop = true;
            bgMusic.volume = 0.05; // Desired volume
        }

        function tryPlayMusic() {
            if (!bgMusic) initBackgroundMusic();
            bgMusic.play().catch(err => {
                console.warn('Autoplay blocked, waiting for user interaction.');
                document.body.addEventListener('click', () => {
                    bgMusic.play();
                }, { once: true });
            });
        }

        window.addEventListener('DOMContentLoaded', tryPlayMusic);

        function fadeInMusic(targetVolume = 0.3, step = 0.02, interval = 150) {
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


        // 🚀 Start immediately when DOM is ready
        window.addEventListener('DOMContentLoaded', initBackgroundMusic);


        // Try to play after user interaction
        function enableMusic() {
            if (!bgMusic) {
                initBackgroundMusic();
            }
            bgMusic.play().catch(err => {
                console.warn('Autoplay blocked until user interaction:', err);
            });
        }

        // Attach event listener once
        window.addEventListener('click', enableMusic, { once: true });

        // 🎵 Toggle background music on/off
        function toggleMusic() {
            if (!bgMusic) {
                initBackgroundMusic();
            }

            if (bgMusic.paused) {
                bgMusic.play().catch(err => console.warn('Autoplay blocked:', err));
            } else {
                bgMusic.pause();
            }
        }


        // -----------------
        // Initialize
        // -----------------
        loadSoundsFromDB();


    </script>

</body>

</html>