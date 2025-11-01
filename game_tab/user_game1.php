<?php
include "../filter_input.php";
include "../database/db_connect.php";
// include "../get_Objects.php";
// include "../get_Audio.php";



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sight Words Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 40px;
            max-width: 600px;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }
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

        .letter {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            cursor: grab;
            user-select: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
            text-transform: uppercase;
        }

        .letter:hover {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.6);
        }

        .letter:active {
            cursor: grabbing;
            transform: scale(0.95);
        }

        .dropzone {
            min-width: 80px;
            min-height: 80px;
            border: 3px dashed #d1d5db;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 5px;
            background: #f9fafb;
            transition: all 0.3s ease;
            font-size: 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .dropzone:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .dropzone.filled {
            border-style: solid;
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white;
            font-weight: 700;
            border-color: #10b981;
            animation: pop 0.4s ease;
        }

        @keyframes pop {
            0% {
                transform: scale(0.8);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .word-container {
            display: flex;
            margin-bottom: 30px;
            justify-content: center;
        }

        .title {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
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

        .instruction {
            text-align: center;
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .speaker-btn {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
            margin: 20px auto;
        }

        .speaker-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        }

        .speaker-btn:active {
            transform: scale(0.95);
        }

        .music-toggle {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
            display: block;
            margin: 0 auto;
        }

        .music-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.6);
        }

        .music-toggle:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body class="flex flex-col items-center justify-center min-h-screen p-4">
    <!-- Background Stars -->
    <div id="stars"></div>

    <div class="game-container">
        <h1 class="title">🌟 Spell the Word! 🌟</h1>

        <!-- Speaker Button -->
        <div class="speaker-btn" id="speakWordBtn" title="Hear the word">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                <path
                    d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
            </svg>
        </div>
        <!-- Background Music Toggle -->
        <!-- <button class="music-toggle mt-2" onclick="toggleMusic()">🎵 Music</button> -->


        <p class="instruction">Drag the letters to spell the word!</p>
        <p id="feedback" class="text-center text-red-600 font-bold text-xl mb-4 hidden">Try Again!</p>

        <div id="game" class="flex flex-col items-center gap-6">
            <!-- Word Dropzone -->
            <div class="word-container" id="dropzones"></div>

            <!-- Letters -->
            <div class="flex gap-4 flex-wrap justify-center" id="letters"></div>

            <button id="nextBtn" class="btn mt-4">Next Word 🎯</button>
        </div>
    </div>

    <script>
        let bgMusic;


        let words = [];
        let currentWordIndex = 0;
        const audioCache = {}; // ✅ Global cache for letter sounds

        // 🧩 1️⃣ Fetch words (names only)
        async function fetchWords() {
            try {
                const res = await fetch("../get_Objects.php");
                const data = await res.json();

                if (!Array.isArray(data)) {
                    console.error("Invalid response:", data);
                    return;
                }

                words = data.map(item => ({
                    name: item.name.toLowerCase(),
                    id: item.id
                }));

                console.log("Loaded words:", words);
                preloadLetterAudio();
                loadWord();
            } catch (err) {
                console.error("Error fetching words:", err);
            }
        }

        // 🎧 Preload all letter audios
        function preloadLetterAudio() {
            const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
            letters.forEach(letter => {
                const url = `../fetch_letter_audio.php?letter=${encodeURIComponent(letter)}`;
                const a = new Audio(url);
                a.preload = 'auto';
                audioCache[letter] = a;
            });
            console.log("Letter audios preloaded ✅");
        }


        // 🎵 Play audio for a letter (cross-browser)
        async function playLetterAudio(letter) {
            try {
                const audio = audioCache[letter.toUpperCase()] || new Audio(`../fetch_letter_audio.php?letter=${encodeURIComponent(letter.toUpperCase())}`);
                audio.currentTime = 0;
                await audio.play().catch(() => {
                    document.body.addEventListener('click', () => audio.play(), { once: true });
                });
            } catch (err) {
                console.error("Audio playback failed:", err);
            }
        }


        // 🔊 Fetch audio for a word (from DB)
        async function fetchAudioForWord(wordId) {
            try {
                const res = await fetch(`../get_Audio.php?id=${wordId}`);
                const data = await res.json();

                if (data.audio) {
                    return data.audio;
                } else {
                    console.warn("No audio found for word ID:", wordId);
                    return null;
                }
            } catch (err) {
                console.error("Error fetching audio:", err);
                return null;
            }
        }

        // 🔊 Play word audio
        async function playWordAudio() {
            const current = words[currentWordIndex];
            if (!current) return;
            const audioSrc = await fetchAudioForWord(current.id);
            if (audioSrc) {
                const audio = new Audio(audioSrc);
                audio.play().catch(err => console.error("Word audio playback failed:", err));
            }
        }

        // 🌀 Shuffle helper
        function shuffle(array) {
            const arr = [...array];
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        }

        // 🎊 Celebration
        function celebrate() {
            const emojis = ['🎉', '⭐', '🌟', '✨', '🎊', '🏆'];
            for (let i = 0; i < 6; i++) {
                setTimeout(() => {
                    const el = document.createElement('div');
                    el.className = 'celebration';
                    el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                    el.style.left = Math.random() * window.innerWidth + 'px';
                    el.style.top = Math.random() * window.innerHeight + 'px';
                    document.body.appendChild(el);
                    setTimeout(() => el.remove(), 1000);
                }, i * 100);
            }
        }

        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 50; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 2 + 's';
            starsContainer.appendChild(star);
        }

        const dropzonesContainer = document.getElementById('dropzones');
        const lettersContainer = document.getElementById('letters');
        const nextBtn = document.getElementById('nextBtn');
        const speakWordBtn = document.getElementById('speakWordBtn');

        function loadWord() {
            document.getElementById('feedback').classList.add('hidden');
            dropzonesContainer.innerHTML = "";
            lettersContainer.innerHTML = "";

            const word = words[currentWordIndex].name;
            nextBtn.disabled = true;
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');

            setTimeout(() => playWordAudio(), 500);

            // Drop zones
            for (let i = 0; i < word.length; i++) {
                const dz = document.createElement('div');
                dz.className = "dropzone";
                dz.dataset.index = i;
                dz.dataset.expectedLetter = word[i];
                dz.addEventListener('dragover', e => e.preventDefault());
                dz.addEventListener('drop', e => handleDrop(e, dz));
                dropzonesContainer.appendChild(dz);
            }

            // Letters
            const shuffled = shuffle(word.split(''));
            shuffled.forEach(l => {
                const letterDiv = document.createElement('div');
                letterDiv.className = "letter";
                letterDiv.textContent = l;
                letterDiv.draggable = true;
                letterDiv.dataset.letter = l;

                // ✅ Universal playback
                const play = () => playLetterAudio(l);
                letterDiv.addEventListener('click', play);
                letterDiv.addEventListener('mousedown', play);
                letterDiv.addEventListener('touchstart', play);
                letterDiv.addEventListener('dragstart', e => {
                    e.dataTransfer.setData("text", l);
                    play();
                });

                lettersContainer.appendChild(letterDiv);
            });
        }

        function handleDrop(e, dz) {
            e.preventDefault();
            const letter = e.dataTransfer.getData("text");
            const draggedEl = document.querySelector(`.letter[data-letter='${letter}'][draggable='true']`);
            if (draggedEl && !dz.classList.contains('filled')) {
                dz.textContent = letter;
                dz.classList.add('filled');
                draggedEl.remove();
                checkWordComplete();
            }
        }

        function checkWordComplete() {
            const dzs = Array.from(dropzonesContainer.children);
            if (!dzs.some(dz => !dz.classList.contains('filled'))) {
                const completeWord = dzs.map(dz => dz.textContent).join('');
                const expectedWord = words[currentWordIndex].name;

                setTimeout(() => {
                    if (completeWord === expectedWord) {
                        celebrate();
                        dropzonesContainer.innerHTML = "";
                        for (let char of expectedWord) {
                            const confirmed = document.createElement('div');
                            confirmed.className = "dropzone filled";
                            confirmed.textContent = char;
                            dropzonesContainer.appendChild(confirmed);
                        }
                        lettersContainer.innerHTML = "";
                        document.getElementById('feedback').classList.add('hidden');
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        playWordAudio();
                    } else {
                        const feedback = document.getElementById('feedback');
                        feedback.textContent = "❌ Try Again!";
                        feedback.classList.remove('hidden');

                        // Shake animation for feedback
                        feedback.style.animation = "shake 0.4s";
                        setTimeout(() => {
                            feedback.style.animation = "";
                        }, 400);

                        // Reset and reshuffle letters for retry
                        setTimeout(() => {
                            feedback.classList.add('hidden');
                            loadWord(); // reload same word again
                        }, 1200);
                    }
                }, 500);
            }
        }


        speakWordBtn.addEventListener('click', playWordAudio);
        nextBtn.addEventListener('click', () => {
            currentWordIndex++;
            if (currentWordIndex >= words.length) {
                localStorage.setItem("readingGameCompleted", "true");
                celebrate();
                setTimeout(() => {
                    window.location.href = "../Users/dashboard.html";
                }, 3000);
            } else {
                loadWord();
            }
        });


        // Initialize background music
        // 🎵 Auto-start background music (no click needed)
        function initBackgroundMusic() {
            bgMusic = new Audio('/SMARTY-PLAYGROUND/game/sounds/bg_game1.mp3');
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

        // 🟢 Automatically start background music after user interaction


        // 🚀 Start game
        fetchWords();

    </script>
</body>

</html>