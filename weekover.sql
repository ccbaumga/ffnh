DROP PROCEDURE IF EXISTS EndOfWeek;
DELIMITER $$
CREATE PROCEDURE EndOfWeek(IN weekparam INT)
EndOfWeek:BEGIN
DECLARE weekvar INTEGER DEFAULT 0;
DECLARE games INTEGER DEFAULT 0;
DECLARE gamesfinished INTEGER DEFAULT 0;

DECLARE curhometeam INTEGER DEFAULT 0;
DECLARE curawayteam INTEGER DEFAULT 0;
DECLARE curhomepoints DECIMAL(4, 1) DEFAULT 0;
DECLARE curawaypoints DECIMAL(4, 1) DEFAULT 0;
DECLARE curtie VARCHAR(4) DEFAULT '';

DECLARE finished INTEGER DEFAULT 0;
DECLARE cursor_name CURSOR FOR select hometeam, awayteam, TeamPointsThisWeek(weekparam, hometeam), TeamPointsThisWeek(weekparam, awayteam) from fantasymatchups where week = weekparam;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET finished = 1;

/*check whether it is good*/
select currentweek INTO weekvar from globals;
select count(*) INTO games from nflgames where week = weekparam;
select count(*) INTO gamesfinished from nflgames where week = weekparam and status = 'final';
IF weekparam <> weekvar OR games <> gamesfinished THEN
	select 'week given isnt current week or not all games finished';
	LEAVE EndOfWeek;
END IF;

OPEN cursor_name;
getMatchup:LOOP
	FETCH cursor_name INTO curhometeam, curawayteam, curhomepoints, curawaypoints;
	IF finished = 1 THEN 
		LEAVE getMatchup;
	END IF;
	IF curhomepoints > curawaypoints THEN
		update fantasymatchups set winner = curhometeam where hometeam = curhometeam and awayteam = curawayteam and week = weekparam;
		update fantasyteams set wins = wins + 1 where teamid = curhometeam;
		update fantasyteams set losses = losses + 1 where teamid = curawayteam;
	END IF;
	IF curhomepoints < curawaypoints THEN
		update fantasymatchups set winner = curawayteam where hometeam = curhometeam and awayteam = curawayteam and week = weekparam;
		update fantasyteams set wins = wins + 1 where teamid = curawayteam;
		update fantasyteams set losses = losses + 1 where teamid = curhometeam;
	END IF;
	IF curhomepoints = curawaypoints THEN
		select l.weeklytiebreaker INTO curtie from leagues as l
		join fantasyteams as f on l.leagueid = f.league
		where f.teamid = curhometeam;
		IF curtie = 'home' THEN
			update fantasymatchups set winner = curhometeam where hometeam = curhometeam and awayteam = curawayteam and week = weekparam;
			update fantasyteams set wins = wins + 1 where teamid = curhometeam;
			update fantasyteams set losses = losses + 1 where teamid = curawayteam;
		END IF;
		IF curtie = 'ties' THEN
			update fantasyteams set ties = ties + 1 where teamid = curhometeam or teamid = curawayteam;
		END IF;
	END IF;

END LOOP getMatchup;
CLOSE cursor_name;
update globals set currentweek = currentweek + 1;

END $$
DELIMITER ;
	

