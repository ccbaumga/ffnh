DROP PROCEDURE IF EXISTS EndOfGame;
DELIMITER $$
CREATE PROCEDURE EndOfGame(IN weekparam INT, IN hometeamparam VARCHAR(3))
BEGIN
DECLARE curawayteam varchar(3) DEFAULT "";
DECLARE curhomescore INT DEFAULT -1;
DECLARE curawayscore INT DEFAULT -1;
DECLARE cursor_name CURSOR FOR select awayteam, homescore, awayscore from nflgames where week = weekparam and hometeam = hometeamparam;
OPEN cursor_name;
FETCH cursor_name INTO curawayteam, curhomescore, curawayscore;
IF curhomescore > curawayscore THEN
	update nflteams set wins = wins + 1 where abbr = hometeamparam;
	update nflteams set losses = losses + 1 where abbr = curawayteam;
ELSEIF curhomescore < curawayscore THEN
	update nflteams set losses = losses + 1 where abbr = hometeamparam;
	update nflteams set wins = wins + 1 where abbr = curawayteam;
ELSEIF curhomescore = curawayscore THEN
	update nflteams set ties = ties + 1 where abbr = hometeamparam;
	update nflteams set ties = ties + 1 where abbr = curawayteam;
END IF;

CLOSE cursor_name;

END$$
DELIMITER ;
