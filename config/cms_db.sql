-- Course Management System Database
-- Run this file in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS cms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cms_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'instructor', 'student') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(100) DEFAULT NULL,
    academic_year ENUM('First Year','Second Year','Third Year','Fourth Year') DEFAULT 'First Year',
    instructor_id INT DEFAULT NULL,
    status ENUM('active','draft') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin account (password: admin_pass_2026)
INSERT INTO users (name, email, password, role, status) VALUES
('Admin User', 'admin@cms.com', '$2y$10$6mzrKnL5ByXba5UEsUQFUe4/15e78zj/Me8z4NTnKge60ahIOTN86', 'admin', 'active');

-- Sample instructors (password: instructor123)
INSERT INTO users (name, email, password, role, status) VALUES
('Vikram Joshi', 'vikram@cms.com', '$2y$10$W2szW3lXFiqjZM4N6Eq01.ytGdN6vrmrtBBAGpsKsjZ/BC2euuYie', 'instructor', 'active'),
('Prof. Michael Jones', 'michael@cms.com', '$2y$10$W2szW3lXFiqjZM4N6Eq01.ytGdN6vrmrtBBAGpsKsjZ/BC2euuYie', 'instructor', 'active'),
('Dr. Anita Sharma', 'anita@cms.com', '$2y$10$W2szW3lXFiqjZM4N6Eq01.ytGdN6vrmrtBBAGpsKsjZ/BC2euuYie', 'instructor', 'active');

-- Sample students (password: student123)
INSERT INTO users (name, email, password, role, status) VALUES
('Alice Johnson', 'alice@cms.com', '$2y$10$iJ7jwvbAPx7OsTmd8Dm0EeRIz3vcOrycZWaoVaUAdvqZCYZsVdXiq', 'student', 'active'),
('Bob Williams', 'bob@cms.com', '$2y$10$iJ7jwvbAPx7OsTmd8Dm0EeRIz3vcOrycZWaoVaUAdvqZCYZsVdXiq', 'student', 'active');

-- Sample courses
INSERT INTO courses (title, description, category, academic_year, instructor_id, status) VALUES
('C Programming', 'Master the fundamentals of procedural programming using C. Cover memory management, pointers, and data structures.', 'Programming', 'First Year', 3, 'active'),
('HTML & CSS Basics', 'The essential building blocks of the web. Design responsive, modern layouts using Flexbox, Grid, and CSS variables.', 'Web Development', 'First Year', 4, 'active'),
('C++ Mastery', 'Advanced object-oriented programming concepts including templates, STL, and modern C++20 features.', 'Programming', 'Second Year', 2, 'active'),
('JavaScript Essentials', 'Deep dive into JavaScript. From DOM manipulation to asynchronous programming and modern ES6+ syntax.', 'Web Development', 'Second Year', 4, 'active'),
('PHP Web Development', 'Build dynamic, database-driven websites using PHP and MySQL. Learn about MVC architecture.', 'Web Development', 'Third Year', 2, 'active'),
('Advanced Web Frameworks', 'Explore modern web frameworks and architectural patterns for large-scale applications.', 'Web Development', 'Third Year', 3, 'active'),
('Data Science with Python', 'Discover the power of data. Learn data analysis, visualization, and machine learning using Python.', 'Data Science', 'Fourth Year', 4, 'active'),
('React Native Mobile', 'Build cross-platform mobile apps for iOS and Android using React Native and JavaScript.', 'Mobile Development', 'Fourth Year', 2, 'active'),
('Cyber Security Fundamentals', 'Protect systems and networks from digital attacks. Learn about encryption, firewalls, and security protocols.', 'Cyber Security', 'Fourth Year', 3, 'active');

-- Sample enrollments
INSERT INTO enrollments (student_id, course_id) VALUES
(5, 1), (5, 2), (6, 1), (6, 3);
