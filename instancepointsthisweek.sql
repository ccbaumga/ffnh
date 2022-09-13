DROP FUNCTION IF EXISTS PointsThisWeek;
DELIMITER $$
CREATE FUNCTION PointsThisWeek(weekparam INT, nflteamparam VARCHAR(3), instanceparam INT)
RETURNS DECIMAL(4, 1)
BEGIN
DECLARE points DECIMAL(4, 1) DEFAULT 0;
DECLARE won BOOLEAN DEFAULT FALSE;
DECLARE nodata INT DEFAULT 0;
DECLARE cursor_1 CURSOR FOR 
	select 1 from nflgames  
	where hometeam = nflteamparam and week = weekparam 
	and status = 'final' and homescore > awayscore;
DECLARE cursor_2 CURSOR FOR 
	select 1 from nflgames  
	where awayteam = nflteamparam and week = weekparam 
	and status = 'final' and homescore < awayscore;
DECLARE cursor_3 CURSOR FOR 
	select 0.5 from nflgames  
	where (hometeam = nflteamparam or awayteam = nflteamparam) and week = weekparam 
	and status = 'final' and homescore = awayscore;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET nodata = 1;
OPEN cursor_1;
FETCH cursor_1 INTO points;
CLOSE cursor_1;
OPEN cursor_2;
FETCH cursor_2 INTO points;
CLOSE cursor_2;
OPEN cursor_3;
FETCH cursor_3 INTO points;
CLOSE cursor_3;
RETURN points * instanceparam;
END$$
DELIMITER ;