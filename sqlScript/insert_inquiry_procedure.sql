DELIMITER //

CREATE PROCEDURE insert_inquiry(
    IN p_name VARCHAR(100),
    IN p_query TEXT,
    IN p_country VARCHAR(100),
    IN p_std_code VARCHAR(10),
    IN p_phone VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_courseid INT,
    IN p_deleteflag TINYINT,
    IN p_status_id INT,
    IN p_remarks TEXT
)
BEGIN
    INSERT INTO inquiry (name, query, country, std_code, phone, email, courseid, deleteflag, status_id, remarks)
    VALUES (p_name, p_query, p_country, p_std_code, p_phone, p_email, p_courseid, p_deleteflag, p_status_id, p_remarks);
END //

DELIMITER ;