-- Create database
CREATE DATABASE IF NOT EXISTS u683763345_kmit_db;
USE u683763345_kmit_db;

-- Student table
CREATE TABLE IF NOT EXISTS student (
    studentid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    date_of_joining DATE NOT NULL DEFAULT (CURRENT_DATE),
	firstname varchar(100),
	lastname varchar(100)
);

DELIMITER $$
DROP PROCEDURE IF EXISTS add_student$$
CREATE PROCEDURE add_student(
    IN in_username VARCHAR(50),
    IN in_password VARCHAR(255),
    IN in_email VARCHAR(100),
    IN in_mobile VARCHAR(20),
    IN in_firstname VARCHAR(100),
    IN in_lastname VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM student WHERE username = in_username) THEN
        INSERT INTO student (username, password, email, mobile, firstname, lastname)
        VALUES (in_username, in_password, in_email, in_mobile, in_firstname, in_lastname);
    END IF;
END$$

DROP PROCEDURE IF EXISTS update_student_by_username$$
CREATE PROCEDURE update_student_by_username(
    IN in_username VARCHAR(50),
    IN in_password VARCHAR(255),
    IN in_email VARCHAR(100),
    IN in_mobile VARCHAR(20),
    IN in_firstname VARCHAR(100),
    IN in_lastname VARCHAR(100)
)
BEGIN
    UPDATE student
    SET
        password = IFNULL(in_password, password),
        email = IFNULL(in_email, email),
        mobile = IFNULL(in_mobile, mobile),
        firstname = IFNULL(in_firstname, firstname),
        lastname = IFNULL(in_lastname, lastname)
    WHERE username = in_username;
END$$

DROP PROCEDURE IF EXISTS delete_student_by_username$$
CREATE PROCEDURE delete_student_by_username(
    IN in_username VARCHAR(50)
)
BEGIN
    DELETE FROM student WHERE username = in_username;
END$$

DROP PROCEDURE IF EXISTS list_all_students$$
CREATE PROCEDURE list_all_students()
BEGIN
    SELECT * FROM student;
END$$

DROP PROCEDURE IF EXISTS get_studentid_by_username$$
CREATE PROCEDURE get_studentid_by_username(
    IN in_username VARCHAR(50)
)
BEGIN
    SELECT studentid FROM student WHERE username = in_username;
END$$
-- Get student by mobile
DROP PROCEDURE IF EXISTS get_student_by_mobile$$
CREATE PROCEDURE get_student_by_mobile(
    IN in_mobile VARCHAR(20)
)
BEGIN
    SELECT * FROM student WHERE mobile = in_mobile;
END$$

-- Get student by email
DROP PROCEDURE IF EXISTS get_student_by_email$$
CREATE PROCEDURE get_student_by_email(
    IN in_email VARCHAR(100)
)
BEGIN
    SELECT * FROM student WHERE email = in_email;
END$$


DELIMITER $$
CREATE PROCEDURE update_student_password(
    IN in_username VARCHAR(50),
    IN in_old_password VARCHAR(255),
    IN in_new_password VARCHAR(255)
)
BEGIN
    DECLARE current_password VARCHAR(255);
    SELECT password INTO current_password FROM student WHERE username = in_username;
    IF current_password IS NULL THEN
        SELECT 'User not found' AS message;
    ELSEIF current_password = in_new_password THEN
        SELECT 'Password is not updated because new password is same as old' AS message;
    ELSEIF in_old_password = '' THEN
        -- Admin reset: skip old password check
        UPDATE student SET password = in_new_password WHERE username = in_username;
        SELECT 'Password updated successfully' AS message;
    ELSEIF current_password = in_old_password THEN
        UPDATE student SET password = in_new_password WHERE username = in_username;
        SELECT 'Password updated successfully' AS message;
    ELSE
        SELECT 'Old password does not match' AS message;
    END IF;
END$$

DELIMITER ;-- Get Student by username
DROP PROCEDURE IF EXISTS get_studentid_by_username;
DELIMITER $$
CREATE PROCEDURE get_studentid_by_username(
    IN in_username VARCHAR(50)
)
BEGIN
    SELECT studentid FROM student WHERE username = in_username;
END $$
DELIMITER ;



--  Student permission using role table
-- Role table
CREATE TABLE IF NOT EXISTS role (
    roleid INT AUTO_INCREMENT PRIMARY KEY,
    rolename VARCHAR(50) NOT NULL UNIQUE
);

-- StudentRole table (many-to-many)
CREATE TABLE IF NOT EXISTS student_role (
    studentid INT NOT NULL,
    roleid INT NOT NULL,
    PRIMARY KEY (studentid, roleid),
    FOREIGN KEY (studentid) REFERENCES student(studentid) ON DELETE CASCADE,
    FOREIGN KEY (roleid) REFERENCES role(roleid) ON DELETE CASCADE
);

DELIMITER $$
-- Get roles for a student (returns all roles, can be filtered for admin in PHP or SQL)
DROP PROCEDURE IF EXISTS get_student_roles$$
CREATE PROCEDURE get_student_roles(
    IN in_username VARCHAR(50)
)
BEGIN
    SELECT r.rolename
    FROM student s
    JOIN student_role sr ON s.studentid = sr.studentid
    JOIN role r ON sr.roleid = r.roleid
    WHERE s.username = in_username;
END$$


-- Get roles for a student (returns all roles, can be filtered for admin in PHP or SQL)
DROP PROCEDURE IF EXISTS get_student_roles$$
CREATE PROCEDURE get_studentid_roles(
    IN in_studentid VARCHAR(50)
)
BEGIN
    SELECT r.rolename
    FROM student s
    JOIN student_role sr ON s.studentid = sr.studentid
    JOIN role r ON sr.roleid = r.roleid
    WHERE s.studentid = in_studentid;
END$$

-- Get if student is admin (returns 1 row if admin, 0 rows if not)
DROP PROCEDURE IF EXISTS is_student_admin$$
CREATE PROCEDURE is_student_admin(
    IN in_username VARCHAR(50)
)
BEGIN
    SELECT r.rolename
    FROM student s
    JOIN student_role sr ON s.studentid = sr.studentid
    JOIN role r ON sr.roleid = r.roleid
    WHERE s.username = in_username AND r.rolename = 'admin';
END$$
Delimiter ;
-- roles ends here

-- Courses table Starts from here
CREATE TABLE IF NOT EXISTS course (
  courseid INT AUTO_INCREMENT PRIMARY KEY,
  coursename VARCHAR(100) NOT NULL,
  image VARCHAR(255),
  description TEXT,
  page_slug VARCHAR(128) NOT NULL DEFAULT '',
  price DECIMAL(10,2) NULL,
  Type Varchar(10) DEFAULT 'PAID'
);

-- Course Category Table
CREATE TABLE IF NOT EXISTS course_category (
    categoryid INT AUTO_INCREMENT PRIMARY KEY,
    categoryname VARCHAR(100) NOT NULL UNIQUE
);

-- Course-Category Link Table (many-to-many)
CREATE TABLE IF NOT EXISTS course_category_link (
    courseid INT NOT NULL,
    categoryid INT NOT NULL,
    PRIMARY KEY (courseid, categoryid),
    FOREIGN KEY (courseid) REFERENCES course(courseid) ON DELETE CASCADE,
    FOREIGN KEY (categoryid) REFERENCES course_category(categoryid) ON DELETE CASCADE
);

---Course and Category procedures
DELIMITER $$

CREATE PROCEDURE AddCourse (
    IN p_coursename VARCHAR(100),
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_page_slug VARCHAR(128),
    IN p_price DECIMAL(10,2),
    IN p_type VARCHAR(10)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    INSERT INTO course (
        coursename, image, description, page_slug, price, Type
    ) VALUES (
        p_coursename, p_image, p_description, p_page_slug, p_price, p_type
    );
    
    COMMIT;
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE UpdateCourse (
    IN p_courseid INT,
    IN p_coursename VARCHAR(100),
    IN p_image VARCHAR(255),
    IN p_description TEXT,
    IN p_page_slug VARCHAR(128),
    IN p_price DECIMAL(10,2),
    IN p_type VARCHAR(10)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    UPDATE course
    SET
        coursename = p_coursename,
        image = p_image,
        description = p_description,
        page_slug = p_page_slug,
        price = p_price,
        Type = p_type
    WHERE courseid = p_courseid;
    
    COMMIT;
END$$

DELIMITER $$

CREATE PROCEDURE ListAllCategories()
BEGIN
    SELECT 
        categoryid,
        categoryname
    FROM 
        course_category
    ORDER BY 
        categoryname;
END$$

DELIMITER ;



DELIMITER $$

CREATE PROCEDURE AddCategory (
    IN p_categoryname VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    INSERT INTO course_category (categoryname)
    VALUES (p_categoryname);
    
    COMMIT;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE UpdateCategory (
    IN p_categoryid INT,
    IN p_categoryname VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE course_category
    SET categoryname = p_categoryname
    WHERE categoryid = p_categoryid;

    COMMIT;
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE DeleteCategory (
    IN p_categoryid INT
)
BEGIN
    DECLARE category_count INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    SELECT COUNT(*) INTO category_count
    FROM course_category_link
    WHERE categoryid = p_categoryid;

    IF category_count = 0 THEN
        START TRANSACTION;
        DELETE FROM course_category WHERE categoryid = p_categoryid;
        COMMIT;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete category: It is linked to one or more courses.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE AddCourseToCategory (
    IN p_courseid INT,
    IN p_categoryid INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;

    INSERT INTO course_category_link (courseid, categoryid)
    VALUES (p_courseid, p_categoryid);

    COMMIT;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE GetCourseIdByName (
    IN p_coursename VARCHAR(100),
    OUT p_courseid INT
)
BEGIN
    SELECT courseid INTO p_courseid
    FROM course
    WHERE coursename = p_coursename
    LIMIT 1;

    IF p_courseid IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Course name not found.';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE GetCategoryIdByName (
    IN p_categoryname VARCHAR(100),
    OUT p_categoryid INT
)
BEGIN
    SELECT categoryid INTO p_categoryid
    FROM course_category
    WHERE categoryname = p_categoryname
    LIMIT 1;

    IF p_categoryid IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Category name not found.';
    END IF;
END$$

DELIMITER ;


DELIMITER ;


DELIMITER $$

CREATE PROCEDURE DeleteCourse (
    IN p_courseid INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;
    
    START TRANSACTION;
    
    DELETE FROM course WHERE courseid = p_courseid;
    
    COMMIT;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE ListCoursesWithCategories()
BEGIN
    SELECT 
        c.courseid,
        c.coursename,
        c.image,
        c.description,
        c.page_slug,
        c.price,
        c.Type,
        cc.categoryid,
        cc.categoryname
    FROM 
        course c
    JOIN 
        course_category_link ccl ON c.courseid = ccl.courseid
    JOIN 
        course_category cc ON ccl.categoryid = cc.categoryid
    ORDER BY 
        c.courseid, cc.categoryname;
END$$

DELIMITER ;

--quizes

DELIMITER //

CREATE PROCEDURE add_level(IN p_level_name VARCHAR(100))
BEGIN
    INSERT INTO levels (level_name)
    VALUES (p_level_name);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE update_level(IN p_level_id INT, IN p_new_level_name VARCHAR(100))
BEGIN
    UPDATE levels
    SET level_name = p_new_level_name
    WHERE level_id = p_level_id;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE delete_level(IN p_level_id INT)
BEGIN
    DELETE FROM levels
    WHERE level_id = p_level_id;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE list_levels()
BEGIN
    SELECT * FROM levels;
END //

DELIMITER ;


-- Drop existing tables if they exist (in reverse dependency order)
DROP TABLE IF EXISTS quiz_attempts;
DROP TABLE IF EXISTS answers;
DROP TABLE IF EXISTS options;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS quizzes;
DROP TABLE IF EXISTS exam_courses;
DROP TABLE IF EXISTS exams;
DROP TABLE IF EXISTS levels;



-- Create Levels Table
CREATE TABLE levels (
    level_id INT AUTO_INCREMENT PRIMARY KEY,
    level_name VARCHAR(100) NOT NULL
);

-- Create Exams Table
CREATE TABLE exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(255) NOT NULL
);
-- Create Exam-Course Mapping Table (Many-to-Many)
CREATE TABLE exam_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
	course_id int not null,
	level_id INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES course(courseid) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id) ON DELETE CASCADE,
	FOREIGN KEY (level_id) REFERENCES levels(level_id) ON DELETE CASCADE
    -- FOREIGN KEY (course_id) REFERENCES courses(course_id) -- Uncomment if courses table exists
);

DELIMITER $$

DELIMITER $$

CREATE PROCEDURE get_all_exams_with_course_and_level()
BEGIN
    SELECT 
        e.exam_id,
        e.exam_name,
        c.courseid,
        c.coursename,
        l.level_id,
        l.level_name
    FROM 
        exams e
    JOIN 
        exam_courses ec ON e.exam_id = ec.exam_id
    JOIN 
        course c ON ec.course_id = c.courseid
    JOIN 
        levels l ON ec.level_id = l.level_id
    ORDER BY 
        e.exam_id, c.courseid, l.level_id;
END$$

DELIMITER ;

CREATE PROCEDURE add_exam_with_course (
    IN p_exam_name VARCHAR(255),
    IN p_course_id INT,
    IN p_level_id INT
)
BEGIN
    DECLARE v_exam_id INT;

    -- Insert into exams table
    INSERT INTO exams (exam_name)
    VALUES (p_exam_name);

    -- Get the last inserted exam_id
    SET v_exam_id = LAST_INSERT_ID();

    -- Insert into exam_courses table
    INSERT INTO exam_courses (exam_id, course_id, level_id)
    VALUES (v_exam_id, p_course_id, p_level_id);
END$$

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE update_exam_with_course (
    IN p_exam_id INT,
    IN p_exam_name VARCHAR(255),
    IN p_course_id INT,
    IN p_level_id INT
)
BEGIN
    -- Update exam name
    UPDATE exams
    SET exam_name = p_exam_name
    WHERE exam_id = p_exam_id;

    -- Update exam_courses mapping (assumes one mapping per exam)
    UPDATE exam_courses
    SET course_id = p_course_id,
        level_id = p_level_id
    WHERE exam_id = p_exam_id;
END$$

DELIMITER ;

DELIMITER $$

CREATE PROCEDURE delete_exam_by_id (
    IN p_exam_id INT
)
BEGIN
    DELETE FROM exams
    WHERE exam_id = p_exam_id;
END$$

DELIMITER ;

-- Create Quizzes Table
CREATE TABLE quizzes (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_code VARCHAR(50) UNIQUE NOT NULL,
    quiz_name VARCHAR(255) NOT NULL,
    exam_id INT NOT NULL,
    
    course_id int not null,
    FOREIGN KEY (course_id) REFERENCES course(courseid) ON DELETE CASCADE, -- Uncomment if you have a courses table
    FOREIGN KEY (exam_id) REFERENCES exams(exam_id) ON DELETE CASCADE
    
);


-- Create the Questions table
CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
);

-- Create the Options table
CREATE TABLE options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE
);

-- Create the Answers table (Correct answers)
CREATE TABLE answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    correct_option_id INT NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    FOREIGN KEY (correct_option_id) REFERENCES options(option_id) ON DELETE CASCADE
);

-- Create the Quiz Attempts table (Student results)
CREATE TABLE quiz_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    quiz_id INT NOT NULL,
    course_id INT NOT NULL,
    score INT NOT NULL,
    attempt_number INT NOT NULL,
    attempt_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
    -- FOREIGN KEY (student_id) REFERENCES students(student_id),
    -- FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Optional: You can create students and courses tables if needed.
-- Example:
-- CREATE TABLE students (student_id INT PRIMARY KEY, student_name VARCHAR(255));
-- CREATE TABLE courses (course_id INT PRIMARY KEY, course_name VARCHAR(255));

DELIMITER $$


DELIMITER $$

CREATE PROCEDURE GetAllQuizzesWithCourseAndExam()
BEGIN
    SELECT 
        q.quiz_id,
        q.quiz_code,
        q.quiz_name,
        c.coursename AS course_name,
        e.exam_name AS exam_name
    FROM quizzes q
    INNER JOIN course c ON q.course_id = c.courseid
    INNER JOIN exams e ON q.exam_id = e.exam_id;
END $$

DELIMITER ;


drop procedure AddQuiz;
DELIMITER $$

CREATE PROCEDURE AddQuiz (
    IN p_quiz_code VARCHAR(50),
    IN p_quiz_name VARCHAR(255),
    IN p_course_id INT,
    IN p_exam_id INT
)
BEGIN
    INSERT INTO quizzes (quiz_code, quiz_name, course_id, exam_id)
    VALUES (p_quiz_code, p_quiz_name, p_course_id, p_exam_id);
END $$

DELIMITER ;

-- 2. Add a Question to a Quiz
CREATE PROCEDURE AddQuestion (
    IN p_quiz_id INT,
    IN p_question_text TEXT
)
BEGIN
    INSERT INTO questions (quiz_id, question_text)
    VALUES (p_quiz_id, p_question_text);
END $$

-- 3. Add an Option to a Question
CREATE PROCEDURE AddOption (
    IN p_question_id INT,
    IN p_option_text TEXT
)
BEGIN
    INSERT INTO options (question_id, option_text)
    VALUES (p_question_id, p_option_text);
END $$

-- 4. Add the Correct Answer for a Question
CREATE PROCEDURE AddAnswer (
    IN p_question_id INT,
    IN p_correct_option_id INT
)
BEGIN
    INSERT INTO answers (question_id, correct_option_id)
    VALUES (p_question_id, p_correct_option_id);
END $$

-- 5. Add a Quiz Attempt
CREATE PROCEDURE AddQuizAttempt (
    IN p_student_id INT,
    IN p_quiz_id INT,
    IN p_course_id INT,
    IN p_score INT,
    IN p_attempt_number INT
)
BEGIN
    INSERT INTO quiz_attempts (student_id, quiz_id, course_id, score, attempt_number)
    VALUES (p_student_id, p_quiz_id, p_course_id, p_score, p_attempt_number);
END $$

-- 6. Delete a Quiz Attempt
CREATE PROCEDURE DeleteQuizAttempt (
    IN p_attempt_id INT
)
BEGIN
    DELETE FROM quiz_attempts
    WHERE attempt_id = p_attempt_id;
END $$

-- 7. Update a Quiz Attempt Score
CREATE PROCEDURE UpdateQuizAttempt (
    IN p_attempt_id INT,
    IN p_score INT
)
BEGIN
    UPDATE quiz_attempts
    SET score = p_score
    WHERE attempt_id = p_attempt_id;
END $$

-- 8. Get All Quiz Attempts by a Student
CREATE PROCEDURE GetQuizAttemptsByStudent (
    IN p_student_id INT
)
BEGIN
    SELECT * FROM quiz_attempts
    WHERE student_id = p_student_id;
END $$

DELIMITER ;

-- quizes ends

-- Contact submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  courseid INT NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (courseid) REFERENCES course(courseid)
);

-- Update contact_submissions to use studentid instead of email
ALTER TABLE contact_submissions
    ADD COLUMN studentid INT,
    DROP COLUMN email,
    ADD CONSTRAINT fk_student FOREIGN KEY (studentid) REFERENCES student(studentid);


-- Insert sample courses
INSERT INTO course (coursename, page_slug, price) VALUES
  ('AWS Solution Architect', 'Aws/SAA.php', 9440),
  ('Azure System Admin', 'Azure/AzureFundamentals.php', 9440),
  ('DevOps', 'DevOps/devops.php', 9440);

-- Insert default roles
INSERT IGNORE INTO role (rolename) VALUES ('Admin'), ('Reader');

-- Stored Procedure: Get all courses
DROP PROCEDURE IF EXISTS get_all_courses;
DELIMITER $$
CREATE PROCEDURE get_all_courses()
BEGIN
  SELECT courseid, coursename, image, description, page_slug, price FROM course;
END $$
DELIMITER ;

-- Stored Procedure: Insert contact submission
DROP PROCEDURE IF EXISTS add_contact_submission;
DELIMITER $$
CREATE PROCEDURE add_contact_submission(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_courseid INT
)
BEGIN
    INSERT INTO contact_submissions (name, phone, courseid)
    VALUES (p_name, p_phone, p_courseid);
END $$
DELIMITER ;

-- Stored Procedure: Get course by ID
DROP PROCEDURE IF EXISTS get_course_by_id;
DELIMITER $$
CREATE PROCEDURE get_course_by_id(IN p_courseid INT)
BEGIN
    SELECT courseid, coursename, image, description, page_slug, price
    FROM course
    WHERE courseid = p_courseid;
END $$
DELIMITER ;

-- Stored Procedure: Get user by credentials
DROP PROCEDURE IF EXISTS get_user_by_credentials;
DELIMITER $$
CREATE PROCEDURE get_user_by_credentials(IN in_username VARCHAR(50), IN dummy VARCHAR(10))
BEGIN
    SELECT username, password FROM student WHERE username = in_username LIMIT 1;
END $$
DELIMITER ;

-- Stored Procedure: Add new student

-- Stored Procedure: Get all course categories
DROP PROCEDURE IF EXISTS get_all_categories;
DELIMITER $$
CREATE PROCEDURE get_all_categories()
BEGIN
    SELECT categoryid, categoryname FROM course_category ORDER BY categoryname;
END $$
DELIMITER ;

-- Stored Procedure: Get courses by category
DROP PROCEDURE IF EXISTS get_courses_by_category;
DELIMITER $$
CREATE PROCEDURE get_courses_by_category(IN in_categoryid INT)
BEGIN
    SELECT c.courseid, c.coursename, c.image, c.page_slug, c.description, c.price
    FROM course c
    JOIN course_category_link l ON c.courseid = l.courseid
    WHERE l.categoryid = in_categoryid
    ORDER BY c.coursename;
END $$
DELIMITER ;

---- live batches
-- Table: livebatches
CREATE TABLE IF NOT EXISTS livebatches (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    mode VARCHAR(50) NOT NULL,
    courseid INT NOT NULL,
    startdate DATE NOT NULL,
    enddate DATE NOT NULL,
    FOREIGN KEY (courseid) REFERENCES course(courseid)
);

-- Table: student_batch

CREATE TABLE IF NOT EXISTS student_batch (
    studentid INT NOT NULL,
    batchid INT NOT NULL,
    joined_at DATETIME NOT NULL,
    PRIMARY KEY (studentid, batchid),
    FOREIGN KEY (studentid) REFERENCES student(studentid),
    FOREIGN KEY (batchid) REFERENCES livebatches(Id)
);

-- Procedure: get_live_batches_by_courseid
DROP PROCEDURE IF EXISTS get_live_batches_by_courseid;
DELIMITER $$
CREATE PROCEDURE get_live_batches_by_courseid(IN in_courseid INT)
BEGIN
    SELECT Id, mode, courseid, startdate, enddate
    FROM livebatches
    WHERE courseid = in_courseid
    ORDER BY startdate;
END$$
DELIMITER ;

-- Procedure: get_student_batches
DROP PROCEDURE IF EXISTS get_student_batches;
DELIMITER $$
CREATE PROCEDURE get_student_batches(IN in_studentid INT)
BEGIN
    SELECT b.Id, b.mode, b.courseid, b.startdate, b.enddate
    FROM livebatches b
    JOIN student_batch sb ON b.Id = sb.batchid
    WHERE sb.studentid = in_studentid
    ORDER BY b.startdate;
END$$
DELIMITER ;


-- Table: payments
CREATE TABLE IF NOT EXISTS payments (
    paymentid INT AUTO_INCREMENT PRIMARY KEY,
    studentid INT NOT NULL,
    batchid INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    payment_reference VARCHAR(128),
    FOREIGN KEY (studentid) REFERENCES student(studentid),
    FOREIGN KEY (batchid) REFERENCES livebatches(Id)
);

DELIMITER ;

DROP PROCEDURE IF EXISTS enroll_student_to_webinar;
DELIMITER $$
CREATE PROCEDURE enroll_student_to_webinar(
    IN in_firstname VARCHAR(100),
    IN in_lastname VARCHAR(100),
    IN in_email VARCHAR(100),
    IN in_mobile VARCHAR(20),
    IN in_webinarid INT
)
BEGIN
    DECLARE sid INT;
    -- Check if student exists
    SELECT studentid INTO sid FROM student WHERE LOWER(email) = LOWER(in_email) LIMIT 1;
    IF sid IS NULL THEN
        -- Insert new student (username and password left NULL for webinar-only signups)
        INSERT INTO student (firstname, lastname, email, mobile, date_of_joining)
        VALUES (in_firstname, in_lastname, in_email, in_mobile, CURRENT_DATE);
        SET sid = LAST_INSERT_ID();
    ELSE
        -- Update mobile, firstname, lastname if not set
        IF (SELECT mobile FROM student WHERE studentid = sid) IS NULL OR (SELECT mobile FROM student WHERE studentid = sid) = '' THEN
            UPDATE student SET mobile = in_mobile WHERE studentid = sid;
        END IF;
        IF (SELECT firstname FROM student WHERE studentid = sid) IS NULL OR (SELECT firstname FROM student WHERE studentid = sid) = '' THEN
            UPDATE student SET firstname = in_firstname WHERE studentid = sid;
        END IF;
        IF (SELECT lastname FROM student WHERE studentid = sid) IS NULL OR (SELECT lastname FROM student WHERE studentid = sid) = '' THEN
            UPDATE student SET lastname = in_lastname WHERE studentid = sid;
        END IF;
    END IF;
    -- Enroll student to webinar if not already enrolled
    IF NOT EXISTS (SELECT 1 FROM student_webinar WHERE studentid = sid AND webinarid = in_webinarid) THEN
        INSERT INTO student_webinar (studentid, webinarid) VALUES (sid, in_webinarid);
    END IF;
END$$
DELIMITER ;


-- Table: webinars
CREATE TABLE IF NOT EXISTS webinars (
    webinarid INT AUTO_INCREMENT PRIMARY KEY,
    courseid INT NOT NULL,
    subtopic VARCHAR(255) NOT NULL,
    webinar_date DATETIME NOT NULL,
    meeting_link VARCHAR(512) NOT NULL,
    is_paid BOOLEAN NOT NULL DEFAULT 0,
    amount DECIMAL(10,2) DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (courseid) REFERENCES course(courseid)
);


-- Table: student_webinar (enrollments)
CREATE TABLE IF NOT EXISTS student_webinar (
    studentid INT NOT NULL,
    webinarid INT NOT NULL,
    enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (studentid, webinarid),
    FOREIGN KEY (studentid) REFERENCES student(studentid),
    FOREIGN KEY (webinarid) REFERENCES webinars(webinarid)
);
ALTER TABLE student_webinar
DROP FOREIGN KEY student_webinar_ibfk_1;

ALTER TABLE student_webinar
ADD CONSTRAINT student_webinar_ibfk_1
  FOREIGN KEY (studentid) REFERENCES student(studentid)
  ON DELETE CASCADE;
--- Webinars
-- Table: webinar_payments (for paid webinars)
CREATE TABLE IF NOT EXISTS webinar_payments (
    paymentid INT AUTO_INCREMENT PRIMARY KEY,
    studentid INT NOT NULL,
    webinarid INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME NOT NULL,
    payment_status VARCHAR(32) NOT NULL DEFAULT 'pending',
    payment_reference VARCHAR(128),
    FOREIGN KEY (studentid) REFERENCES student(studentid),
    FOREIGN KEY (webinarid) REFERENCES webinars(webinarid)
);
DROP PROCEDURE IF EXISTS get_upcoming_webinars;
DELIMITER $$
CREATE PROCEDURE get_upcoming_webinars()
BEGIN
    SELECT w.webinarid, w.courseid, w.subtopic, w.webinar_date, w.meeting_link, w.is_paid, w.amount, w.created_at
    FROM webinars w
    WHERE w.webinar_date >= NOW()
    ORDER BY w.webinar_date ASC;
END$$
DELIMITER ;

-- Procedure: get_webinar_students
DROP PROCEDURE IF EXISTS get_webinar_students;
DELIMITER $$
CREATE PROCEDURE get_webinar_students(IN in_webinarid INT)
BEGIN
    SELECT s.studentid, s.firstname, s.lastname, s.email, sw.enrolled_at
    FROM student s
    JOIN student_webinar sw ON s.studentid = sw.studentid
    WHERE sw.webinarid = in_webinarid
    ORDER BY sw.enrolled_at;
END$$
DELIMITER ;

-- Procedure: enroll_student_to_webinar
DROP PROCEDURE IF EXISTS enroll_student_to_webinar;
DELIMITER $$
CREATE PROCEDURE enroll_student_to_webinar(
    IN in_firstname VARCHAR(100),
    IN in_lastname VARCHAR(100),
    IN in_email VARCHAR(255),
    IN in_phone VARCHAR(20),
    IN in_webinarid INT
)
BEGIN
    DECLARE sid INT;
    -- Check if student exists
    SELECT studentid INTO sid FROM student WHERE LOWER(email) = LOWER(in_email) LIMIT 1;
    IF sid IS NULL THEN
        -- Insert new student
        INSERT INTO student (firstname, lastname, email, phone) VALUES (in_firstname, in_lastname, in_email, in_phone);
        SET sid = LAST_INSERT_ID();
    ELSE
        -- Update phone if not set
        IF (SELECT phone FROM student WHERE studentid = sid) IS NULL OR (SELECT phone FROM student WHERE studentid = sid) = '' THEN
            UPDATE student SET phone = in_phone WHERE studentid = sid;
        END IF;
    END IF;
    -- Enroll student to webinar if not already enrolled
    IF NOT EXISTS (SELECT 1 FROM student_webinar WHERE studentid = sid AND webinarid = in_webinarid) THEN
        INSERT INTO student_webinar (studentid, webinarid) VALUES (sid, in_webinarid);
    END IF;
END$$
DELIMITER ;


