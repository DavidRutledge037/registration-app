-- Create the database
CREATE DATABASE IF NOT EXISTS registration_app;
USE registration_app;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT NOT NULL,
    semester ENUM('Spring', 'Summer', 'Fall') NOT NULL,
    year YEAR NOT NULL,
    status ENUM('Open', 'Closed', 'Waitlist') DEFAULT 'Open'
);

-- Enrollments table
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Dropped', 'Completed') DEFAULT 'Active',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Waitlist table
CREATE TABLE waitlist (
    waitlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    course_id INT,
    waitlist_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    position INT,
    status ENUM('Waiting', 'Notified', 'Enrolled', 'Expired') DEFAULT 'Waiting',
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Create indexes
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_course_semester_year ON courses(semester, year);
CREATE INDEX idx_enrollment_status ON enrollments(status);
CREATE INDEX idx_waitlist_status ON waitlist(status);

-- Insert sample courses
INSERT INTO courses (course_code, course_name, description, capacity, semester, year) VALUES
('CST310', 'Software Development', 'Introduction to software development principles', 30, 'Fall', 2024),
('CST320', 'Database Design', 'Fundamentals of database design and implementation', 25, 'Fall', 2024),
('CST330', 'Web Development', 'Modern web development techniques and practices', 35, 'Spring', 2025);
