insert into profiles (username, password) values ('cal', 'vin');
insert into leagues (leaguename, admin) values ('neenah', 'cal');
insert into fantasyteams (league, owner, teamname) 
	select leagueid, 'cal', 'vikes'
	from leagues where leaguename = 'neenah';
insert into profiles (username, password) values ('ryan', 'isgay');
insert into fantasyteams (league, owner, teamname) 
	select leagueid, 'ryan', 'packies'
	from leagues where leaguename = 'neenah';
insert into profiles (username, password) values ('bra', 'poo');
insert into fantasyteams (league, owner, teamname) 
	select leagueid, 'bra', 'poopbears'
	from leagues where leaguename = 'neenah';

INSERT INTO fantasymatchups ( week, hometeam, awayteam ) 
	VALUES(1, 
	(SELECT  teamid FROM fantasyteams WHERE teamname = 'packies'), 
	(SELECT  teamid FROM fantasyteams WHERE teamname = 'vikes'));
	
insert into leagues (leaguename, admin) values ('neenah2', 'ryan');
insert into fantasyteams (league, owner, teamname) 
	select leagueid, 'cal', 'vikes2'
	from leagues where leaguename = 'neenah2';
insert into fantasyteams (league, owner, teamname) 
	select leagueid, 'ryan', 'packies2'
	from leagues where leaguename = 'neenah2';

INSERT INTO fantasymatchups ( week, hometeam, awayteam ) 
	VALUES(1, 
	(SELECT  teamid FROM fantasyteams WHERE teamname = 'packies2'), 
	(SELECT  teamid FROM fantasyteams WHERE teamname = 'vikes2'));
	
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'GB', 1, 'starting');
insert into nflgames (week, hometeam, awayteam, status, day, kickofftime) 
	values (1, 'GB', 'MIN', 'upcoming', 'Sun', '13:00:00');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'MIN', 1, 'starting');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'WAS', 1, 'bench');
insert into nflgames (week, hometeam, awayteam, status, day, kickofftime) 
	values (1, 'WAS', 'DAL', 'upcoming', 'Sun', '15:30:00');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'PHI', 1, 'bench');
insert into nflgames (week, hometeam, awayteam, status, day, kickofftime, 
	homescore, awayscore, quarter, clock) 
	values (1, 'PHI', 'PIT', 'ongoing', 'Sun', '15:30:00', 7, 13, 2, '00:14:16');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'PIT', 1, 'starting');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'CAR', 1, 'starting');
insert into nflteaminstances (league, instancenumber, nflteam, owner, status) 
	values (1, 1, 'ATL', 1, 'bench');
insert into nflgames (week, hometeam, awayteam, status, day, kickofftime, 
	homescore, awayscore, quarter, clock) 
	values (1, 'CAR', 'ATL', 'final', 'Sun', '15:30:00', 19, 17, 4, '00:00:00');