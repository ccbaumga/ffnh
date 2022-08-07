delete from nflgames;
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "NE", "LA", "Thu", "19:20:00", 3, 24, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "TEN", "JAX", "Sat", "15:30:00", 31, 10, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "MIN", "TB", "Sat", "19:15:00",14, 26, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "KC", "MIA", "Sun", "12:00:00",33, 27, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "DEN", "CAR", "Sun", "12:00:00",32, 27, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "HOU", "CHI", "Sun", "12:00:00", 7, 36, "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "ARI", "NYG", "Sun", "12:00:00",26, 7,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "DAL", "CIN", "Sun", "12:00:00",30, 7,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "IND", "LV", "Sun", "12:00:00",44, 27,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "NYJ", "SEA", "Sun", "12:00:00",3, 40,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "GB", "DET", "Sun", "15:30:00",31, 24,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "ATL", "LAC", "Sun", "15:30:00",17, 20,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "WAS", "SF", "Sun", "15:30:00",23, 15,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "NO", "PHI", "Sun", "19:20:00",21, 24,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "PIT", "BUF", "Mon", "19:15:00",15, 26,  "final");
insert into nflgames (week, awayteam, hometeam, day, kickofftime, awayscore, homescore, status) values
(1, "BAL", "CLE", "Sun", "12:00:00", 47, 42,  "final");


UPDATE nflteams set wins = 10, losses = 3
where abbr = "BUF";
UPDATE nflteams set wins = 8, losses = 5
where abbr = "mia";
UPDATE nflteams set wins = 6, losses = 7
where abbr = "ne";
UPDATE nflteams set wins = 0, losses = 13
where abbr = "nyj";
UPDATE nflteams set wins = 12, losses = 1
where abbr = "kc";
UPDATE nflteams set wins = 7, losses = 6
where abbr = "lv";
UPDATE nflteams set wins = 5, losses = 8
where abbr = "den";
UPDATE nflteams set wins = 4, losses = 9
where abbr = "lac";
UPDATE nflteams set wins = 11, losses = 2
where abbr = "pit";
UPDATE nflteams set wins = 9, losses = 4
where abbr = "cle";
UPDATE nflteams set wins = 8, losses = 5
where abbr = "bal";
UPDATE nflteams set wins = 2, losses = 10, ties = 1
where abbr = "cin";
UPDATE nflteams set wins = 9, losses = 4
where abbr = "ten";
UPDATE nflteams set wins = 9, losses = 4
where abbr = "ind";
UPDATE nflteams set wins = 4, losses = 9
where abbr = "hou";
UPDATE nflteams set wins = 1, losses = 12
where abbr = "jax";
UPDATE nflteams set wins = 6, losses = 7
where abbr = "was";
UPDATE nflteams set wins = 5, losses = 8
where abbr = "nyg";
UPDATE nflteams set wins = 4, losses = 8, ties = 1
where abbr = "phi";
UPDATE nflteams set wins = 4, losses = 9
where abbr = "dal";
UPDATE nflteams set wins = 9, losses = 4
where abbr = "la";
UPDATE nflteams set wins = 9, losses = 4
where abbr = "sea";
UPDATE nflteams set wins = 7, losses = 6
where abbr = "ari";
UPDATE nflteams set wins = 5, losses = 8
where abbr = "sf";
UPDATE nflteams set wins = 10, losses = 3
where abbr = "gb";
UPDATE nflteams set wins = 6, losses = 7
where abbr = "min";
UPDATE nflteams set wins = 6, losses = 7
where abbr = "chi";
UPDATE nflteams set wins = 5, losses = 8
where abbr = "det";
UPDATE nflteams set wins = 10, losses = 3
where abbr = "no";
UPDATE nflteams set wins = 8, losses = 5
where abbr = "tb";
UPDATE nflteams set wins = 4, losses = 9
where abbr = "atl";
UPDATE nflteams set wins = 4, losses = 9
where abbr = "car";

UPDATE nflteaminstances set owner = 2, status = "bench" where nflteam = "gb"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "bench" where nflteam = "min"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "starting" where nflteam = "ind"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 2, status = "starting" where nflteam = "was"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 2, status = "starting" where nflteam = "atl"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 2, status = "bench" where nflteam = "lv"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "starting" where nflteam = "ari"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "bench" where nflteam = "car"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 2, status = "starting" where nflteam = "pit"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "starting" where nflteam = "sea"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 2, status = "starting" where nflteam = "nyg"
and instancenumber = 1 and league = 2;
UPDATE nflteaminstances set owner = 3, status = "starting" where nflteam = "nyj"
and instancenumber = 1 and league = 2;
