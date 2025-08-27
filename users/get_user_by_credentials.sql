DELIMITER $$
CREATE PROCEDURE get_user_by_credentials(IN in_username VARCHAR(50), IN in_password VARCHAR(255))
BEGIN
    DECLARE db_password VARCHAR(255);
    DECLARE db_username VARCHAR(50);
    SELECT username, password INTO db_username, db_password FROM student WHERE username = in_username LIMIT 1;
    IF db_username IS NOT NULL AND db_password IS NOT NULL THEN
        IF db_password = in_password THEN
            SELECT db_username AS username;
        ELSE
            SELECT NULL AS username;
        END IF;
    ELSE
        SELECT NULL AS username;
    END IF;
END$$
DELIMITER ;
