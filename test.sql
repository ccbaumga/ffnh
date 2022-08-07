source tableCreation.sql;
source createTeams.sql;
source sampleleague.sql;
select * from leagues;
select * from fantasyteams;
/*select * from fantasymatchups;*/

/*select week, t1.teamname hometeamname, t2.teamname awayteamname, m.winner
	from fantasymatchups m
	join fantasyteams t1 on t1.teamid = m.hometeam
	join fantasyteams t2 on t2.teamid = m.awayteam;*/

select l.leaguename, week, t1.teamname hometeamname, t2.teamname awayteamname, m.winner
	from fantasymatchups m
	join fantasyteams t1 on t1.teamid = m.hometeam
	join fantasyteams t2 on t2.teamid = m.awayteam
	join leagues l on l.leagueid = t1.league;
	
/*select l.leaguename, week, t1.teamname hometeamname, t2.teamname awayteamname, m.winner
	from fantasymatchups m
	join fantasyteams t1 on t1.teamid = m.hometeam
	join fantasyteams t2 on t2.teamid = m.awayteam
	join leagues l on l.leagueid = t1.league
	where week = 1
	and t1.league = 1;*/

/*select a.week, b.teamname, b.teamname
	from fantasymatchups a, fantasyteams b
	where a.hometeam = b.teamid;*/
	
select * from nflteaminstances;