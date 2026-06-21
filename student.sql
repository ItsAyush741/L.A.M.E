-- student.sql
-- Run this FIRST before book.sql and transaction.sql

CREATE TABLE IF NOT EXISTS student (
    student_id VARCHAR(20) PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL
);

-- Sample data (plain-text passwords for development only)
-- In production, use password_hash() in PHP and store hashed values here.
INSERT IGNORE INTO student (student_id, name, password) VALUES
    ('S001', 'Alice Johnson', 'pass123'),
    ('S002', 'Bob Smith',    'pass456');
