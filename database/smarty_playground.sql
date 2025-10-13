CREATE DATABASE IF NOT EXISTS smarty_playground
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smarty_playground;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'student') DEFAULT 'student',
  points INT DEFAULT 0,
  status ENUM('active', 'inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_progress (
    progress_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points INT DEFAULT 0,
    level INT DEFAULT 1,
    missions_completed INT DEFAULT 0,
    lessons_completed INT DEFAULT 0,
    progress_percent INT DEFAULT 0 CHECK (progress_percent BETWEEN 0 AND 100),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_user_progress_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

CREATE TABLE missions (
    mission_id INT AUTO_INCREMENT PRIMARY KEY,
    mission_name VARCHAR(100) NOT NULL,
    mission_description TEXT,
    points_reward INT DEFAULT 0 CHECK (points_reward >= 0),
    level_required INT DEFAULT 1 CHECK (level_required >= 1),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_missions (
    user_mission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mission_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (mission_id) REFERENCES missions(mission_id) ON DELETE CASCADE
);

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

CREATE TABLE user_lessons (
    user_lesson_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
);


CREATE TABLE letter_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    letter CHAR(1) NOT NULL UNIQUE,
    audio LONGBLOB NOT NULL
);


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

CREATE TABLE beginning_sounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    word VARCHAR(50) NOT NULL,
    image_path VARCHAR(255) NOT NULL,  -- store the file path instead of the image
    sound_letter CHAR(1) NOT NULL
);
CREATE TABLE beginning_sounds_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_letter CHAR(1) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES beginning_sounds(id)
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

INSERT INTO game_sounds (key_name, label, file_path, image_path) VALUES
('dog',  'Dog',  'image/dog.wav',  'image/dog.png'),
('cat',  'Cat',  'image/cat.wav',  'image/cat.png'),
('car',  'Car',  'image/car.wav',  'image/car.png'),
('bell', 'Bell', 'image/bell.wav', 'image/bell.png'),
('bird', 'Bird', 'image/bird.wav', 'image/bird.png'),
('rain', 'Rain', 'image/rain.wav', 'image/rain.png');


CREATE TABLE game4_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,   -- Path to image, e.g., 'images/cat.png'
    correct VARCHAR(50) NOT NULL,
    wrong VARCHAR(50) NOT NULL
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