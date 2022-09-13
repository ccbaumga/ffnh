DROP FUNCTION IF EXISTS PointsSeason;
DELIMITER $$
CREATE FUNCTION PointsSeason(nflteamparam VARCHAR(3), instanceparam INT)
RETURNS DECIMAL(4, 1)
BEGIN
DECLARE points DECIMAL(4, 1) DEFAULT 0;
DECLARE nodata INT DEFAULT 0;
DECLARE cursor_1 CURSOR FOR 
	select (wins + ties/2.0) * instanceparam from nflteams 
	where abbr = nflteamparam;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET nodata = 1;
OPEN cursor_1;
FETCH cursor_1 INTO points;
CLOSE cursor_1;
RETURN points;
END$$
DELIMITER ;