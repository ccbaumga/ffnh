DROP PROCEDURE IF EXISTS EndOfWeek;
DELIMITER $$
CREATE PROCEDURE EndOfWeek(IN curweek INT)
BEGIN
SELECT
currentweek
FROM
globals;
END$$
DELIMITER ;