DROP FUNCTION IF EXISTS TeamPointsThisWeek;
DELIMITER $$
CREATE FUNCTION TeamPointsThisWeek(weekparam INT, teamid INT)
RETURNS DECIMAL(4, 1)
BEGIN
DECLARE points INT DEFAULT 0;
select SUM(PointsThisWeek(weekparam, nflteam, instancenumber)) INTO points from nflteaminstances 
where owner = teamid and status = 'starting';
IF points IS NULL THEN
	SET points = 0;
END IF;
RETURN points;
END$$
DELIMITER ;