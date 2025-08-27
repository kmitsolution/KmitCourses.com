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
-- Get student roles by student ID
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

-- Get student by username (for search)
DROP PROCEDURE IF EXISTS get_student_by_username$$
CREATE PROCEDURE get_student_by_username(
    IN in_username VARCHAR(50)
)
BEGIN
    SELECT * FROM student WHERE username = in_username;
END$$
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
DROP PROCEDURE IF EXISTS update_student_password$$
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
    ELSEIF current_password = in_old_password THEN
        UPDATE student SET password = in_new_password WHERE username = in_username;
        SELECT 'Password updated successfully' AS message;
    ELSE
        SELECT 'Old password does not match' AS message;
    END IF;
END$$
DELIMITER ;
