-- Fix collation issues in the database
-- Set all tables and columns to use utf8mb4_unicode_ci for consistency

USE u683763345_kmit_db;

-- Set database collation
ALTER DATABASE u683763345_kmit_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Alter student table columns to use utf8mb4_unicode_ci
ALTER TABLE student
    MODIFY COLUMN username VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
    MODIFY COLUMN password VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN email VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
    MODIFY COLUMN mobile VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN firstname VARCHAR(100) COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN lastname VARCHAR(100) COLLATE utf8mb4_unicode_ci;

-- Alter role table
ALTER TABLE role
    MODIFY COLUMN rolename VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE;

-- Alter course table
ALTER TABLE course
    MODIFY COLUMN coursename VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN image VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN description TEXT COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN page_slug VARCHAR(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    MODIFY COLUMN Type VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT 'PAID';

-- Alter course_category table
ALTER TABLE course_category
    MODIFY COLUMN categoryname VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE;

-- Alter contact_submissions table
ALTER TABLE contact_submissions
    MODIFY COLUMN name VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN email VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN phone VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter livebatches table
ALTER TABLE livebatches
    MODIFY COLUMN mode VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter payments table
ALTER TABLE payments
    MODIFY COLUMN payment_status VARCHAR(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
    MODIFY COLUMN payment_reference VARCHAR(128) COLLATE utf8mb4_unicode_ci;

-- Alter webinars table
ALTER TABLE webinars
    MODIFY COLUMN subtopic VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    MODIFY COLUMN meeting_link VARCHAR(512) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter levels table
ALTER TABLE levels
    MODIFY COLUMN level_name VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter exams table
ALTER TABLE exams
    MODIFY COLUMN exam_name VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter quizzes table
ALTER TABLE quizzes
    MODIFY COLUMN quiz_code VARCHAR(50) COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL,
    MODIFY COLUMN quiz_name VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter questions table
ALTER TABLE questions
    MODIFY COLUMN question_text TEXT COLLATE utf8mb4_unicode_ci NOT NULL;

-- Alter options table
ALTER TABLE options
    MODIFY COLUMN option_text TEXT COLLATE utf8mb4_unicode_ci NOT NULL;

-- Add more ALTER statements for other tables if needed