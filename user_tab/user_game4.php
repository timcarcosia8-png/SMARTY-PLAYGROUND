<?php
include 'filter_input.php';
include 'db_connect.php';
// include "user_session.php";
// include "../get_Objects.php";
// include "../get_Audio.php";



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Lesson 4: Medial Short /a/ Sound</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&display=swap" rel="stylesheet" />
<style>
  body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    .phone-container {
      max-width: 400px;
      margin: 0 auto;
      background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    .game-container {
      background: white;
      border-radius: 30px 30px 0 0;
      box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
      padding: 30px 20px 20px;
      flex: 1;
      margin-top: 80px;
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
      font-size: 1.75rem;
      text-align: center;
      margin-bottom: 8px;
    }

    .subtitle {
      text-align: center;
      color: #6b7280;
      font-size: 0.9rem;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .progress-container {
      background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
      border-radius: 20px;
      padding: 15px;
      text-align: center;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .progress-label {
      color: white;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .progress-value {
      color: white;
      font-size: 2rem;
      font-weight: 700;
    }

    .progress-dots {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 10px;
      flex-wrap: wrap;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.4);
      transition: all 0.3s ease;
    }

    .dot.completed {
      background: white;
      box-shadow: 0 2px 8px rgba(255, 255, 255, 0.6);
    }

    .dot.current {
      background: white;
      transform: scale(1.4);
      box-shadow: 0 2px 8px rgba(255, 255, 255, 0.8);
    }

    .image-box {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-radius: 24px;
      padding: 40px 20px;
      text-align: center;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(251, 191, 36, 0.2);
      transition: all 0.3s ease;
    }

    .image-box:hover {
      transform: scale(1.02);
      box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
    }

    .emoji {
      font-size: 100px;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }

    .message-box {
      background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
      border-radius: 20px;
      padding: 16px;
      text-align: center;
      font-size: 1rem;
      font-weight: 600;
      color: #5b21b6;
      margin-bottom: 20px;
      min-height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
    }

    .buttons-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 15px;
    }

    .answer-button {
      font-size: 1.5rem;
      font-weight: 700;
      padding: 20px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      width: 100%;
      font-family: 'Fredoka', sans-serif;
    }

    .answer-button:hover:not(:disabled) {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    .answer-button:active:not(:disabled) {
      transform: translateY(-2px);
    }

    .answer-button:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .button-option-1 {
      background: linear-gradient(135deg, #fde047 0%, #fb923c 100%);
      color: #1f2937;
      border: 3px solid #facc15;
    }

    .button-option-2 {
      background: linear-gradient(135deg, #86efac 0%, #5eead4 100%);
      color: #1f2937;
      border: 3px solid #22c55e;
    }

    .star {
      position: absolute;
      background: white;
      border-radius: 50%;
      animation: twinkle 3s infinite ease-in-out;
    }

    @keyframes twinkle {
      0%, 100% {
        opacity: 0.2;
        transform: scale(1);
      }
      50% {
        opacity: 1;
        transform: scale(1.2);
      }
    }

    .celebration {
      position: fixed;
      font-size: 3rem;
      pointer-events: none;
      animation: celebrate 1s ease-out;
      z-index: 1000;
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

    .header-section {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      padding: 24px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 10;
    }

    .back-btn {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: 2px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
      text-decoration: none;
      color: white;
      font-size: 1.5rem;
    }

    .back-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }

    .header-title-mobile {
      color: white;
      font-size: 1.25rem;
      font-weight: 700;
      flex: 1;
      text-align: center;
      margin: 0 12px;
    }

    .result-container {
      text-align: center;
      padding: 20px 0;
    }

    .result-emoji {
      font-size: 80px;
      margin-bottom: 20px;
      animation: celebrate-result 0.6s ease;
    }

    @keyframes celebrate-result {
      0%, 100% { transform: scale(1) rotate(0deg); }
      25% { transform: scale(1.2) rotate(-10deg); }
      75% { transform: scale(1.2) rotate(10deg); }
    }

    .result-title {
      font-size: 2rem;
      color: #9333ea;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .result-score-box {
      background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
      border-radius: 20px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
    }

    .result-score-label {
      color: white;
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .result-score {
      color: white;
      font-size: 3rem;
      font-weight: 700;
    }

    .play-again-button {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
      color: white;
      font-size: 1.3rem;
      font-weight: 700;
      padding: 18px 40px;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
      transition: all 0.3s ease;
      font-family: 'Fredoka', sans-serif;
      width: 100%;
    }

    .play-again-button:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5);
    }

    .confetti-piece {
      position: fixed;
      width: 10px;
      height: 10px;
      z-index: 1000;
      animation: confetti-fall 3s linear forwards;
    }

    @keyframes confetti-fall {
      to { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
</style>
</head>
<body>
<div class="phone-container">
  <div id="stars"></div>

  <div class="header-section">
    <a href="user_dashboard.php" class="back-btn">←</a>
    <div class="header-title-mobile">📚 Medial /a/ Sound</div>
    <div style="width:48px;"></div>
  </div>

  <div class="game-container" id="gameContainer"></div>
</div>

<script>
    let bgMusic;
    
let questions = [];
let currentQ = 0;
let score = 0;
let totalTries = 0;
let hasAnswered = false;
let currentAudio = null;

const gameContainer = document.getElementById('gameContainer');

function createStars() {
  const starsContainer = document.getElementById('stars');
  for (let i = 0; i < 30; i++) {
    const star = document.createElement('div');
    star.className = 'star';
    const size = Math.random()*3+1;
    star.style.width=size+'px';
    star.style.height=size+'px';
    star.style.left=Math.random()*100+'%';
    star.style.top=Math.random()*60+'%';
    star.style.animationDelay=Math.random()*3+'s';
    star.style.animationDuration=(Math.random()*2+2)+'s';
    starsContainer.appendChild(star);
  }
}

async function loadQuestions(){
  try{
    const res = await fetch('get_question.php');
    if(!res.ok) throw new Error('Failed to fetch questions');
    questions = await res.json();
    if(questions.length>0) renderGame();
    else gameContainer.innerHTML='<p class="text-center text-red-500">No questions found.</p>';
  }catch(err){
    console.error(err);
    gameContainer.innerHTML='<p class="text-center text-red-500">Error loading questions.</p>';
  }
}

function playSound(file){
  if(currentAudio){currentAudio.pause(); currentAudio.currentTime=0;}
  currentAudio = new Audio(file);
  currentAudio.play().catch(err=>console.warn('Audio failed:',err));
}

function celebrate(){
  const emojis=['🎉','⭐','🌟','✨','🎊','🏆'];
  for(let i=0;i<6;i++){
    setTimeout(()=>{
      const c=document.createElement('div');
      c.className='celebration';
      c.textContent=emojis[Math.floor(Math.random()*emojis.length)];
      c.style.left=Math.random()*window.innerWidth+'px';
      c.style.top=Math.random()*window.innerHeight+'px';
      document.body.appendChild(c);
      setTimeout(()=>c.remove(),1000);
    }, i*100);
  }
}

function renderGame(){
  hasAnswered=false;
  const q = questions[currentQ];
  gameContainer.innerHTML=`
    <h1 class="title">📚 Medial Short /a/ Sound</h1>
    <p class="subtitle">Listen to both words first, then choose the one with /a/!</p>

    <div class="progress-container">
      <div class="progress-label">Your Score</div>
      <div class="progress-value">${score} / ${totalTries}</div>
      <div class="progress-dots">
        ${questions.map((_,i)=>`<div class="dot ${i<currentQ?'completed':''} ${i===currentQ?'current':''}"></div>`).join('')}
      </div>
    </div>

        <div class="image-box" id="emojiBox">
      <img src="${q.image}" alt="question image" class="emoji" />
    </div>

    <div class="message-box" id="message">🔊 Click a button to hear the word first!</div>

    <div class="buttons-container">
      <button class="answer-button button-option-1" id="btn1">${q.correct}</button>
      <button class="answer-button button-option-2" id="btn2">${q.wrong}</button>
    </div>
  `;

  document.getElementById('emojiBox').addEventListener('click', ()=>playSound(q.correctAudio));

  let btn1Clicked=false, btn2Clicked=false;
  document.getElementById('btn1').addEventListener('click', function(){
    if(!hasAnswered){
      if(!btn1Clicked){ playSound(q.correctAudio); btn1Clicked=true; }
      else handleAnswer(q.correct);
    }
  });
  document.getElementById('btn2').addEventListener('click', function(){
    if(!hasAnswered){
      if(!btn2Clicked){ playSound(q.wrongAudio); btn2Clicked=true; }
      else handleAnswer(q.wrong);
    }
  });
}

function handleAnswer(selected){
  if(hasAnswered) return;
  hasAnswered=true;
  const q = questions[currentQ];
  const isCorrect = selected === q.correct;
  totalTries++;
  if(isCorrect) { score++; celebrate(); }
  const msg=document.getElementById('message');
  msg.textContent = isCorrect ? `✨ Correct! "${q.correct}" has the /a/ sound!` : `💫 Not quite! Correct: "${q.correct}"`;
  setTimeout(()=>{
    if(currentQ<questions.length-1){ currentQ++; renderGame(); }
    else renderResult();
  },2000);
}

function renderResult(){
  const percentage = Math.round((score/totalTries)*100);
  let emoji='🎉', title='Amazing Work!';
  if(percentage===100){ emoji='🏆'; title='Perfect Score!'; }
  else if(percentage>=80){ emoji='⭐'; title='Excellent Job!'; }
  else if(percentage>=60){ emoji='👍'; title='Good Effort!'; }
  else{ emoji='💪'; title='Keep Practicing!'; }

  gameContainer.innerHTML = `
        <div class="result-container">
          <div class="result-emoji">${emoji}</div>
          <div class="result-title">${title}</div>
          
          <div class="result-score-box">
            <div class="result-score-label">Final Score</div>
            <div class="result-score">${score} / ${totalTries}</div>
          </div>

          <div style="background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%); border-radius: 20px; padding: 16px; margin-bottom: 20px;">
            <div style="color: #5b21b6; font-weight: 600; font-size: 0.95rem;">
              🎯 Accuracy: ${percentage}%
            </div>
          </div>

          <button class="play-again-button" onclick="restartGame()">
            🔄 Try Again
          </button>
        </div>
      `;
      
      localStorage.setItem("readingGameCompleted", "true");
      
      // Redirect to dashboard after 5 seconds
      setTimeout(() => {
        window.location.href = 'user_dashboard.php';
      }, 5000);
}

function restartGame(){ currentQ=0; score=0; totalTries=0; hasAnswered=false; renderGame(); }

    function initBackgroundMusic() {
            bgMusic = new Audio('bg_game4.mp3');
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
        // function enableMusic() {
        //     if (!bgMusic) {
        //         initBackgroundMusic();
        //     }
        //     bgMusic.play().catch(err => {
        //         console.warn('Autoplay blocked until user interaction:', err);
        //     });
        // }

        // // Attach event listener once
        // window.addEventListener('click', enableMusic, { once: true });

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
        
         
    

createStars();
loadQuestions();
</script>
</body>
</html>
