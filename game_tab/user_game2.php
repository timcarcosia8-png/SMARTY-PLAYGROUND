<?php
include "../filter_input.php";
include "../database/db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Let Us Try - Lesson 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ==========================
           Styles from your first code
           ========================== */
        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .game-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 650px;
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
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 15px;
        }

        .instruction {
            text-align: center;
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 25px;
            font-weight: 500;
            line-height: 1.6;
        }

        .picture {
            font-size: 100px;
            text-align: center;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .picture-name {
            font-size: 1.8rem;
            font-weight: 700;
            text-align: center;
            color: #4b5563;
            margin: 15px 0;
        }

        .question {
            font-size: 1.3rem;
            font-weight: 600;
            text-align: center;
            color: #374151;
            margin: 25px 0;
        }

        .sound-btn {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            border-radius: 20px;
            padding: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
            border: none;
            text-transform: uppercase;
        }

        .sound-btn:hover {
            transform: scale(1.1) rotate(3deg);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.6);
        }

        .sound-btn:active {
            transform: scale(0.95);
        }

        .sound-btn.correct {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            animation: success 0.5s ease;
        }

        .sound-btn.wrong {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
            animation: shake 0.5s ease;
        }

        @keyframes success {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15) rotate(5deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .btn {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.25rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6);
        }

        .btn:active {
            transform: translateY(0);
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

        .feedback {
            text-align: center;
            font-size: 1.8rem;
            margin-top: 20px;
            min-height: 50px;
            font-weight: 700;
        }

        .feedback.correct {
            color: #10b981;
            animation: fadeIn 0.5s ease;
        }

        .feedback.wrong {
            color: #ef4444;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .progress {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #8b5cf6;
            margin-bottom: 20px;
        }

        .completion {
            text-align: center;
        }

        .completion-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fbbf24;
            margin-bottom: 20px;
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .score {
            font-size: 1.5rem;
            color: #4b5563;
            margin: 20px 0;
            font-weight: 600;
        }
    </style>
</head>

<body class="flex flex-col items-center justify-center min-h-screen p-4">
    <!-- Background Stars -->
    <div id="stars"></div>

    <div class="game-container">
        <div id="gameScreen">
            <h1 class="title">🌟 Beginning Sounds 🌟</h1>

            <p class="instruction">
                Say the name of the picture below.<br />
                Then tap the letter that makes the beginning sound!
            </p>

            <div class="progress" id="progress"></div>

            <div class="picture" id="picture"></div>
            <div class="picture-name" id="pictureName"></div>

            <div class="question">What is the beginning sound?</div>

            <div class="grid grid-cols-2 gap-4 mb-6" id="soundOptions"></div>

            <div class="feedback" id="feedback"></div>

            <div class="text-center">
                <button id="nextBtn" class="btn" onclick="nextQuestion()" style="display: none">
                    Next Question ➡️
                </button>
            </div>
        </div>

        <div id="completionScreen" class="completion" style="display: none">
            <div class="completion-title">🎉 Amazing Work! 🎉</div>
            <div class="score" id="finalScore"></div>
            <button class="btn" onclick="restartGame()">Play Again! 🔄</button>
        </div>
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

        let questions = [];
        let currentQuestion = 0;
        let score = 0;
        let answered = false;
        let currentAudio = null;

        // Map letter sounds
        const letterSounds = {};
        for (let i = 65; i <= 90; i++) {
            const letter = String.fromCharCode(i).toLowerCase();
            letterSounds[letter.toUpperCase()] = `sounds/kevin-${letter}.mp3`;
        }

        function shuffle(array) {
            const arr = [...array];
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        }

        // Fetch questions from DB
        async function loadQuestionsFromDB() {
            try {
                const res = await fetch('../get_Image.php'); // Go up one folder to root

                if (!res.ok) throw new Error('Failed to fetch questions');
                questions = await res.json();

                if (questions.length > 0) {
                    currentQuestion = 0;
                    loadQuestion();
                } else {
                    document.getElementById('gameScreen').innerHTML = '<p>No questions found in DB.</p>';
                }
            } catch (err) {
                console.error("Error loading questions:", err);
                document.getElementById('gameScreen').innerHTML = '<p>Error loading questions. Check console.</p>';
            }
        }

        // Load a single question
        function loadQuestion() {
            answered = false;
            const q = questions[currentQuestion];
            document.getElementById('progress').textContent = `Question ${currentQuestion + 1} of ${questions.length}`;

            const pictureElem = document.getElementById('picture');
            pictureElem.innerHTML = `
                <div class="flex justify-center items-center">
                    <img src="../game/image/${q.word}.png" alt="${q.word}" 
                        class="w-40 h-40 object-contain border-4 border-yellow-400 rounded-2xl bg-white shadow-md">
                </div>
                `;

            pictureElem.style.cursor = 'pointer';
            pictureElem.onclick = () => playWordSound(q.word);

            const feedback = document.getElementById('feedback');
            feedback.textContent = '';
            feedback.className = 'feedback';

            document.getElementById('nextBtn').style.display = 'none';

            const optionsContainer = document.getElementById('soundOptions');
            optionsContainer.innerHTML = '';

            shuffle(q.options).forEach(option => {
                const btn = document.createElement('button');
                btn.className = 'sound-btn';
                btn.textContent = option;
                btn.onclick = () => checkAnswer(option, btn);
                optionsContainer.appendChild(btn);
            });
        }

        // Check answer
        function checkAnswer(selected, btn) {
            if (answered) return;
            playLetterSound(selected);
            answered = true;
            const q = questions[currentQuestion];
            const allBtns = document.querySelectorAll('.sound-btn');

            if (selected === q.sound) {
                btn.classList.add('correct');
                document.getElementById('feedback').textContent = '✨ Excellent! ✨';
                document.getElementById('feedback').className = 'feedback correct';
                celebrate();
                score++;
            } else {
                btn.classList.add('wrong');
                document.getElementById('feedback').textContent = '💫 Try to remember! 💫';
                document.getElementById('feedback').className = 'feedback wrong';
                allBtns.forEach(b => { if (b.textContent === q.sound) b.classList.add('correct'); });
            }

            allBtns.forEach(b => b.style.pointerEvents = 'none');
            setTimeout(() => document.getElementById('nextBtn').style.display = 'inline-block', 1500);
        }

        function nextQuestion() {
            currentQuestion++;
            if (currentQuestion >= questions.length) {
                showCompletion();
            } else {
                loadQuestion();
            }
        }

        // let currentAudio = null;

        // Play any audio file
        function playSound(filePath) {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
            }
            currentAudio = new Audio(filePath);
            currentAudio.play();
        }

        // Play letter sound
        function playWordSound(word) {
            playSound(`../game/sounds/kevin-${word.toLowerCase()}.mp3`);
        }

        function playLetterSound(letter) {
            const soundUrl = `../game/sounds/kevin-${letter.toLowerCase()}.mp3`;
            playSound(soundUrl);
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
        function showCompletion() {
            // Save completion so the dashboard can show the congratulations box
            localStorage.setItem("readingGameCompleted", "true");

            // Hide game screen and show completion screen
            document.getElementById('gameScreen').style.display = 'none';
            const completionScreen = document.getElementById('completionScreen');
            completionScreen.style.display = 'block';

            // Show score
            document.getElementById('finalScore').textContent = `Your score: ${score} / ${questions.length}`;

            // Redirect after a short delay
            setTimeout(() => {
                window.location.href = "../Users/dashboard.html"; // adjust the path if needed
            }, 1000);
        }
        function initBackgroundMusic() {
            bgMusic = new Audio('/SMARTY-PLAYGROUND/game/sounds/bg_game2.mp3');
            bgMusic.loop = true;
            bgMusic.volume = 0.1; // Desired volume
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

        // Load on page start
        loadQuestionsFromDB();
    </script>
</body>

</html>