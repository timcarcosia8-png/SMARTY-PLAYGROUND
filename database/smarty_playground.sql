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

CREATE TABLE objects_audio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,       -- e.g., dog, cat
    audio LONGBLOB NOT NULL           -- store actual mp3 data
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
