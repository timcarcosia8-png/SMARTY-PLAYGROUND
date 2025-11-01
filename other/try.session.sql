CREATE DATABASE IF NOT EXISTS smarty_playground
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE DATABASE smarty_playground;
USE smarty_playground;
drop DATABASE smarty_playground;

DROP TABLE users;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,  -- user profile image path
  role ENUM('admin', 'student') DEFAULT 'student',
  points INT DEFAULT 0,
  verification_code VARCHAR(6) DEFAULT NULL,
  is_verified TINYINT(1) DEFAULT 0,
  status ENUM('active', 'inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


DELETE FROM users WHERE email = 'mr.ocampo12@gmail.com';
SELECT * FROM users;
ALTER TABLE users DROP COLUMN points;

INSERT INTO users (name, email, password, role, status)
VALUES (
  'Administrator', --sample admin user
  'admin@smartyplayground.com', -- sample email
  '$2y$10$8.KR3z7G6UhpQSTUeE.5GuKmQe7wZ7yTSCmMbtT5.C8HksIh2H5xu', -- sample hashed password for admin
  'admin', -- sample role
  'active' -- sample status
); 

INSERT INTO users (name, email, password, role, status)
VALUES (
  'Administrator',
  'admin@smartyplayground.com',
  '$2y$10$8.KR3z7G6UhpQSTUeE.5GuKmQe7wZ7yTSCmMbtT5.C8HksIh2H5xu',
  'admin',
  'active'
);

UPDATE users
SET password = '$2y$10$ZB9nE1CucT9Jb8z4cM.1EehiEmzNnEnyY4aVciK1h1pHXbGgE45ca'
WHERE email = 'admin@smartyplayground.com';

ALTER TABLE users MODIFY password VARCHAR(255);

UPDATE users 
SET password = '$2y$10$.Jj4RPKEwuwB8.ZVSFt/8.xltF6OC3nvcI5N7IEr1zbanb4/a2lBq' 
WHERE email = 'admin@smartyplayground.com';

SELECT email, password FROM users WHERE email = 'admin@smartyplayground.com';


SELECT * FROM users;
-- USE smarty_playground;
INSERT INTO users (name, email, points, status) VALUES
('John Smith','johnsmith@example.com',120,'active'),
('Cameron Williamson','cameronwilliamson@example.com',95,'active'),
('Eleanor Pena','eleanorpena@example.com',130,'active');

SELECT * FROM users;

DELETE FROM users WHERE email = 'johnsmith@example.com';
DELETE FROM users WHERE email = 'cameronwilliamson@example.com';
DELETE FROM users WHERE email = 'eleanorpena@example.com';

DROP TABLE user_progress;

CREATE TABLE user_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    missions_completed INT DEFAULT 0,
    lessons_completed INT DEFAULT 0,
    progress_percent INT DEFAULT 0 CHECK (progress_percent BETWEEN 0 AND 100),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_user_progress_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);


INSERT INTO user_progress (user_id, points, level, missions_completed, lessons_completed)
VALUES
(2, 350, 5, 12, 10),
(3, 200, 3, 8, 6),
(4, 150, 2, 5, 4);

SELECT * FROM user_progress;

DROP TABLE missions;

CREATE TABLE missions (
    mission_id INT AUTO_INCREMENT PRIMARY KEY,
    mission_name VARCHAR(100) NOT NULL,
    mission_description TEXT,
    points_reward INT DEFAULT 0 CHECK (points_reward >= 0),
    level_required INT DEFAULT 1 CHECK (level_required >= 1),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO missions (mission_name, mission_description, points_reward, level_required, status)
VALUES
('Color Match', 'Match colors correctly to earn stars.', 10, 1, 'active'),
('Alphabet Hunt', 'Find all letters from A to Z.', 15, 1, 'active'),
('Shape Sorter', 'Drag shapes to the correct outlines.', 20, 2, 'active'),
('Number Puzzle', 'Solve number puzzles to complete the board.', 25, 2, 'active'),
('Animal Sounds', 'Match animals with their sounds.', 30, 3, 'active'),
('Story Time', 'Listen and answer simple story questions.', 40, 3, 'active'),
('Memory Match', 'Find all the matching cards.', 35, 4, 'active'),
('Word Builder', 'Form words using given letters.', 45, 4, 'active'),
('Quiz Challenge', 'Answer a series of mixed questions.', 50, 5, 'active'),
('Smarty Final Challenge', 'Complete all previous lessons to unlock.', 100, 5, 'active');

SELECT * FROM user_missions;

CREATE TABLE user_missions (
    user_mission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mission_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (mission_id) REFERENCES missions(mission_id) ON DELETE CASCADE
);


INSERT INTO user_missions (user_id, mission_id, completed_at) VALUES
(2, 1, '2025-10-10 09:30:00'),
(2, 2, '2025-10-11 10:45:00'),
(3, 1, '2025-10-09 14:20:00'),
(4, 2, '2025-10-08 16:00:00'),
(4, 3, '2025-10-10 17:30:00');



DROP TABLE IF EXISTS user_missions;
DROP TABLE IF EXISTS user_progress;
DROP TABLE IF EXISTS missions;
DROP TABLE IF EXISTS users;

DROP TABLE IF EXISTS lessons;


CREATE TABLE lessons (
    lesson_id INT AUTO_INCREMENT PRIMARY KEY,
    mission_id INT NOT NULL,
    lesson_title VARCHAR(150) NOT NULL,
    lesson_description TEXT,
    topic VARCHAR(100),
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'easy',
    level_required INT DEFAULT 1,
    points_reward INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (mission_id) REFERENCES missions(mission_id) ON DELETE CASCADE
);

SELECT * FROM lessons;

INSERT INTO lessons (mission_id, lesson_title, lesson_description, topic, difficulty, level_required, points_reward)
VALUES
(2, 'Learning A-Z', 'Identify and pronounce the letters of the alphabet.', 'Alphabet', 'easy', 1, 10),
(4, 'Counting Fun', 'Learn to count numbers from 1 to 10.', 'Numbers', 'easy', 1, 15),
(3, 'Shapes Around Us', 'Recognize basic shapes like circle, square, and triangle.', 'Shapes', 'medium', 2, 20),
(1, 'Color Magic', 'Learn and match colors correctly.', 'Colors', 'medium', 2, 25),
(5, 'Animal Friends', 'Identify different animals and their sounds.', 'Animals', 'hard', 3, 30);



SELECT * FROM user_lessons;

CREATE TABLE user_lessons (
    user_lesson_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
);


INSERT INTO user_lessons (user_id, lesson_id, completed_at)
VALUES
-- Alice completed Alphabet lessons
(2, 11, '2025-10-10 09:00:00'),
(2, 2, '2025-10-10 09:15:00'),

-- Bob completed one Alphabet lesson and one Number lesson
(3, 11, '2025-10-10 10:00:00'),
(3, 13, '2025-10-10 10:20:00');



INSERT INTO user_lessons (user_id, lesson_id, completed_at)
VALUES
-- Alice completed Alphabet lessons
(2, 11, '2025-10-10 09:00:00'),
(2, 12, '2025-10-10 09:15:00'),

-- Bob completed one Alphabet lesson and one Number lesson
(3, 11, '2025-10-10 10:00:00'),
(3, 13, '2025-10-10 10:20:00');



SELECT lesson_id, lesson_title FROM lessons;


CREATE TABLE objects_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,       -- e.g., dog, cat
    audio LONGBLOB NOT NULL           -- store actual mp3 data
);

SELECT * FROM objects_audio;



SELECT * FROM objects_audio;
SELECT id, name, LENGTH(audio) FROM objects_audio;
DELETE FROM objects_audio WHERE name = 'dog';

CREATE TABLE letter_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    letter CHAR(1) NOT NULL UNIQUE,
    audio LONGBLOB NOT NULL
);

CREATE TABLE user_letter_audio_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  letter_id INT NOT NULL,
  attempts INT DEFAULT 0,
  score INT DEFAULT 0,
  last_played TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (letter_id) REFERENCES letter_audio(id)
);


DROP TABLE letter_audio;

INSERT INTO letter_audio (letter, audio)
VALUES
('A', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-A.mp3')),
('B', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-B.mp3')),
('C', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-C.mp3')),
('D', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-D.mp3')),
('E', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-E.mp3')),
('F', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-F.mp3')),
('G', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-G.mp3')),
('H', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-H.mp3')),
('I', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-I.mp3')),
('J', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-J.mp3')),
('K', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-K.mp3')),
('L', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-L.mp3')),
('M', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-M.mp3')),
('N', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-N.mp3')),
('O', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-O.mp3')),
('P', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-P.mp3')),
('Q', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-Q.mp3')),
('R', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-R.mp3')),
('S', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-S.mp3')),
('T', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-T.mp3')),
('U', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-U.mp3')),
('V', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-V.mp3')),
('W', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-W.mp3')),
('X', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-X.mp3')),
('Y', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-Y.mp3')),
('Z', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-Z.mp3'));

SELECT * FROM letter_audio;
SELECT letter, LENGTH(audio) FROM letter_audio;



('tryagain', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/tryagain.mp3')),
('apple', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-apple.mp3')),
('bag', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-bag.mp3')),
('bed', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-bed.mp3')),
('big', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-big.mp3')),
('butterfly', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-butterfly.mp3')),
('cat', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-cat.mp3')),
('dog', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-dog.mp3')),
('elephant', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-elephant.mp3')),
('frog', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-frog.mp3')),
('fun', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-fun.mp3')),
('girraffe', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-girraffe.mp3')),
('hat', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-hat.mp3')),
('house', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-house.mp3')),
('ice cream', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-ice cream.mp3')),
('kangaroo', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-kangaroo.mp3')),
('lion', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-lion.mp3')),
('monkey', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-monkey.mp3')),
('penguin', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-penguin.mp3')),
('rabbit', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-rabbit.mp3')),
('red', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-red.mp3')),
('run', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-run.mp3')),
('sun', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-sun.mp3')),
('turtle', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/sounds/kevin-turtle.mp3'))

use smarty_playground;

CREATE TABLE beginning_sounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    word VARCHAR(50) NOT NULL,
    image_path VARCHAR(255) NOT NULL,  -- store the file path instead of the image
    sound_letter CHAR(1) NOT NULL
);

SELECT * FROM beginning_sounds;

drop TABLE beginning_sounds;
drop Table beginning_sounds_options;

CREATE TABLE beginning_sounds_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_letter CHAR(1) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES beginning_sounds(id)
);

DROP Table user_beginning_sounds_progress;

CREATE TABLE user_beginning_sounds_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  chosen_option CHAR(1),
  is_correct BOOLEAN DEFAULT NULL,
  attempts INT DEFAULT 0,
  last_played TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (question_id) REFERENCES beginning_sounds(id)
);

SELECT * FROM user_beginning_sounds_progress;
SELECT * FROM beginning_sounds_options;


SELECT * FROM beginning_sounds_options;
INSERT INTO beginning_sounds (word, image_path, sound_letter) VALUES
('apple', 'images/apple.png', 'A'),
('butterfly', 'images/butterfly.png', 'B'),
('cat', 'images/cat.png', 'C'),
('dog', 'images/dog.png', 'D'),
('elephant', 'images/elephant.png', 'E'),
('rain', 'images/rain.png', 'R');




-- Question 1: apple (A)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(1, 'A'), (1, 'B'), (1, 'C'), (1, 'D');

-- Question 2: butterfly (B)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(2, 'A'), (2, 'B'), (2, 'C'), (2, 'D');

-- Question 3: cat (C)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(3, 'A'), (3, 'B'), (3, 'C'), (3, 'D');

-- Question 4: dog (D)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(4, 'A'), (4, 'B'), (4, 'C'), (4, 'D');

-- Question 5: elephant (E)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(5, 'A'), (5, 'B'), (5, 'C'), (5, 'E');

-- Question 6: rain (R)
INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
(6, 'P'), (6, 'R'), (6, 'S'), (6, 'T');

SELECT * from beginning_sounds_options;

drop Table beginning_sounds;
drop Table beginning_sounds_options;

INSERT INTO beginning_sounds (word, images, image_type, sound_letter)
VALUES (
    'apple',
    LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/apple.png'),
    'image/png',
    'A'
);


INSERT INTO beginning_sounds (word, images, image_type, sound_letter) VALUES
('apple', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/apple.png'), 'image/png', 'A'),
('butterfly', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/butterfly.png'), 'image/png', 'B'),
('cat', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/cat.png'), 'image/png', 'C'),
('dog', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/dog.png'), 'image/png', 'D'),
('elephant', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/elephant.png'), 'image/png', 'E'),
('rain', LOAD_FILE('C:/xampp/htdocs/SMARTY-PLAYGROUND/game/image/rain.png'), 'image/png', 'R');

SELECT * FROM beginning_sounds;

INSERT INTO beginning_sounds_options (question_id, option_letter) VALUES
-- apple (id = 1)
(1, 'A'), (1, 'B'), (1, 'C'), (1, 'D'),

-- butterfly (id = 2)
(2, 'A'), (2, 'B'), (2, 'F'), (2, 'P'),

-- cat (id = 3)
(3, 'C'), (3, 'K'), (3, 'S'), (3, 'T'),

-- dog (id = 4)
(4, 'B'), (4, 'D'), (4, 'G'), (4, 'P'),

-- elephant (id = 5)
(5, 'A'), (5, 'E'), (5, 'I'), (5, 'L'),

-- rain (id = 6)
(6, 'R'), (6, 'P'), (6, 'T'), (6, 'S');


CREATE TABLE game_sounds (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    key_name VARCHAR(50) NOT NULL,          -- Unique identifier (matches card data-key)
    label VARCHAR(100) NOT NULL,            -- Display name for hints / messages
    file_path VARCHAR(255) NOT NULL,        -- Path to audio file (e.g., "sounds/dog.mp3")
    image_path VARCHAR(255) DEFAULT NULL,   -- Optional: Path to card image
    PRIMARY KEY (id),
    UNIQUE KEY key_name_unique (key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_game3_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  sound_id INT UNSIGNED NOT NULL,
  score INT DEFAULT 0,
  attempts INT DEFAULT 0,
  last_played TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (sound_id) REFERENCES game_sounds(id)
);


INSERT INTO game_sounds (key_name, label, file_path, image_path) VALUES
('dog',  'Dog',  'image/dog.wav',  'image/dog.png'),
('cat',  'Cat',  'image/cat.wav',  'image/cat.png'),
('car',  'Car',  'image/car.wav',  'image/car.png'),
('bell', 'Bell', 'image/bell.wav', 'image/bell.png'),
('bird', 'Bird', 'image/bird.wav', 'image/bird.png'),
('rain', 'Rain', 'image/rain.wav', 'image/rain.png');

DROP TABLE game_sounds;
SELECT * FROM game_sounds;


CREATE TABLE game4_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,   -- Path to image, e.g., 'images/cat.png'
    correct VARCHAR(50) NOT NULL,
    wrong VARCHAR(50) NOT NULL
);

CREATE TABLE user_game4_progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  is_correct BOOLEAN,
  attempts INT DEFAULT 0,
  score INT DEFAULT 0,
  last_played TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (question_id) REFERENCES game4_questions(id)
);


INSERT INTO game4_questions (image, correct, wrong) VALUES
('image/cat.png', 'cat', 'cot'),
('image/hat.png', 'hat', 'hit'),
('image/rat.png', 'rat', 'rot'),
('image/bat.png', 'bat', 'bit'),
('image/map.png', 'map', 'mop'),
('image/cap.png', 'cap', 'cup'),
('image/cab.png', 'cab', 'cub'),
('image/bus.png', 'bus', 'bas');


DROP TABLE game4_questions;
SELECT* FROM game4_questions;


CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO videos (title, description, file_path)
VALUES 
('Phonics For Kids', 'English Phonics Story A to Z', 'lesson1.mp4'),
('The Alphabet From A - Z ', 'Phonics For Kids', 'lesson2.mp4'),
('The Spelling Song', 'Learn to Spell 3 Letter Words ', 'lesson3.mp4'),
('Fun with Three Letter Words', 'Let’s learn three letter words with fun pictures and spellings!', 'lesson4.mp4');
