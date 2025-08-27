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

-- Stored Procedure: Get all categories
DELIMITER $$
CREATE PROCEDURE get_all_categories()
BEGIN
    SELECT categoryid, categoryname FROM course_category ORDER BY categoryname;
END$$
DELIMITER ;

-- Stored Procedure: Get all courses for a category (with image and page_slug)
DELIMITER $$
CREATE PROCEDURE get_courses_by_category(IN in_categoryid INT)
BEGIN
    SELECT c.courseid, c.coursename, c.image, c.page_slug, c.description
    FROM course c
    JOIN course_category_link l ON c.courseid = l.courseid
    WHERE l.categoryid = in_categoryid
    ORDER BY c.coursename;
END$$
DELIMITER ;
