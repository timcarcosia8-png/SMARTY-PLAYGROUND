CREATE DATABASE smarty_playground;
USE smarty_playground;

-- CREATE DATABASE IF NOT EXISTS smarty_playground
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;
-- USE smarty_playground;

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

CREATE TABLE lessons (
  lesson_id INT AUTO_INCREMENT PRIMARY KEY,
  lesson_title VARCHAR(100) NOT NULL,
  description TEXT,
  difficulty ENUM('easy','medium','hard') DEFAULT 'easy',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE progress (
  progress_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  lesson_id INT NOT NULL,
  score INT DEFAULT 0,
  completed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (lesson_id) REFERENCES lessons(lesson_id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role, status)
VALUES (
  'Administrator', --sample admin user
  'admin@smartyplayground.com', -- sample email
  '$2y$10$8.KR3z7G6UhpQSTUeE.5GuKmQe7wZ7yTSCmMbtT5.C8HksIh2H5xu', -- sample hashed password for admin
  'admin', -- sample role
  'active' -- sample status
); 
-- password: admin123

INSERT INTO lessons (lesson_title, description, difficulty) VALUES
('Alphabet Adventure', 'Learn the alphabet through fun mini-games.', 'easy'), --sample lessons
('Number Quest', 'Practice counting and basic math.', 'easy'),
('Shape Explorer', 'Identify shapes and colors.', 'medium');

-- CREATE TABLE posts (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     title VARCHAR(255),
--     content TEXT,
--     FOREIGN KEY (user_id) REFERENCES users(id)
-- );

-- INSERT INTO users (name, email, points, status) VALUES
-- ('John Smith', 'johnsmith@example.com', 120, 'active'),
-- ('Cameron Williamson', 'cameronwilliamson@example.com', 95, 'active'),
-- ('Arlene Simmons', 'arlenesimmons@example.com', 85, 'inactive'),
-- ('Eleanor Pena', 'eleanorpena@example.com', 130, 'active'),
-- ('Wade Warren', 'wadewarren@example.com', 110, 'active');

