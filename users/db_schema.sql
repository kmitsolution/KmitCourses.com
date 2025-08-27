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

-- Insert default roles
INSERT IGNORE INTO role (rolename) VALUES ('Admin'), ('Reader');

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

-- Table: student
CREATE TABLE IF NOT EXISTS student (
    studentid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    date_of_joining DATE NOT NULL DEFAULT (CURRENT_DATE),
    firstname VARCHAR(100),
    lastname VARCHAR(100)
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

-- Procedure: enroll_student_after_payment
DROP PROCEDURE IF EXISTS enroll_student_after_payment;
DELIMITER $$
CREATE PROCEDURE enroll_student_after_payment(
    IN in_studentid INT,
    IN in_batchid INT,
    IN in_amount DECIMAL(10,2),
    IN in_payment_reference VARCHAR(128)
)
BEGIN
    DECLARE already_enrolled INT DEFAULT 0;
    SELECT COUNT(*) INTO already_enrolled FROM student_batch WHERE studentid = in_studentid AND batchid = in_batchid;
    IF already_enrolled = 0 THEN
        INSERT INTO payments (studentid, batchid, amount, payment_date, payment_status, payment_reference)
        VALUES (in_studentid, in_batchid, in_amount, NOW(), 'success', in_payment_reference);
        INSERT INTO student_batch (studentid, batchid, joined_at)
        VALUES (in_studentid, in_batchid, NOW());
    END IF;
END$$
DELIMITER ;

-- Procedure: get_upcoming_webinars
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

-- Demo data for webinars
INSERT INTO webinars (courseid, subtopic, webinar_date, meeting_link, is_paid, amount)
VALUES
    (1, 'AWS Cloud Security Best Practices', '2025-08-25 18:00:00', 'https://meet.example.com/aws-security', 0, 0.00),
    (2, 'Azure DevOps Pipelines Deep Dive', '2025-08-28 19:30:00', 'https://meet.example.com/azure-pipelines', 1, 299.00),
    (3, 'DevOps: CI/CD for Beginners', '2025-09-01 17:00:00', 'https://meet.example.com/devops-cicd', 0, 0.00),
    (1, 'AWS Lambda & Serverless', '2025-09-05 20:00:00', 'https://meet.example.com/aws-lambda', 1, 199.00);
