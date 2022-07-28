DROP TABLE IF EXISTS chats;
DROP TABLE IF EXISTS nflteaminstances;
DROP TABLE IF EXISTS fantasymatchups;
DROP TABLE IF EXISTS fantasyteams;
DROP TABLE IF EXISTS nflgames;
DROP TABLE IF EXISTS nflteams;
DROP TABLE IF EXISTS leagues;
DROP TABLE IF EXISTS profiles;

CREATE TABLE nflteams (
  abbr  VARCHAR(3),
  location    VARCHAR(40),
	mascot   VARCHAR(20),
	teamimage BLOB DEFAULT NULL,
  wins	INTEGER DEFAULT 0, 
  losses	INTEGER DEFAULT 0, 
  ties	INTEGER DEFAULT 0,
  PRIMARY KEY (abbr)
);

CREATE TABLE nflgames (
	week INTEGER, 
	hometeam VARCHAR(3), 
	awayteam VARCHAR(3), 
	day VARCHAR(3), 
	kickofftime TIME, 
	status ENUM ('upcoming', 'ongoing', 'final') DEFAULT 'upcoming', 
	homescore INTEGER, 
	awayscore INTEGER, 
	quarter INTEGER, 
	clock TIME, 
	PRIMARY KEY (week, hometeam, awayteam), 
	FOREIGN KEY(hometeam) REFERENCES nflteams(abbr) ON UPDATE CASCADE, 
	FOREIGN KEY(awayteam) REFERENCES nflteams(abbr) ON UPDATE CASCADE
);
	
CREATE TABLE profiles (
	username VARCHAR(20), 
	password VARCHAR(100), 
	PRIMARY KEY (username)
);

CREATE TABLE leagues (
	leagueid INTEGER AUTO_INCREMENT, 
	leaguename VARCHAR(20), 
	admin VARCHAR(20), 
	statusweek INTEGER DEFAULT -1,  
	maxinstances INTEGER, 
	privacy ENUM ('public', 'private') DEFAULT 'public', 
	rosterlimit INTEGER DEFAULT NULL, 
	maxstart INTEGER DEFAULT NULL, 
	drafttime DATETIME DEFAULT NULL, 
	PRIMARY KEY (leagueid), 
	FOREIGN KEY (admin) REFERENCES profiles(username) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE fantasyteams (
	teamid INTEGER AUTO_INCREMENT, 
	owner VARCHAR(20), 
	league INTEGER, 
	teamname VARCHAR(40), 
	teamimage BLOB DEFAULT NULL, 
	wins	INTEGER DEFAULT 0, 
  losses	INTEGER DEFAULT 0, 
  ties	INTEGER DEFAULT 0,
	PRIMARY KEY (teamid), 
	FOREIGN KEY (owner) REFERENCES profiles(username) ON UPDATE CASCADE ON DELETE SET NULL, 
	FOREIGN KEY (league) REFERENCES leagues(leagueid) ON DELETE CASCADE
);

CREATE TABLE nflteaminstances (
	nflteam VARCHAR(3), 
	league INTEGER, 
	instancenumber INTEGER,
	owner INTEGER DEFAULT NULL, 
	status ENUM ('starting', 'bench') DEFAULT NULL,
	PRIMARY KEY (nflteam, league, instancenumber),
	FOREIGN KEY (nflteam) REFERENCES nflteams(abbr) ON UPDATE CASCADE ON DELETE CASCADE, 
	FOREIGN KEY (league) REFERENCES leagues(leagueid) ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES fantasyteams(teamid) ON DELETE SET NULL
);
	
CREATE TABLE fantasymatchups (
	week INTEGER, 
	hometeam INTEGER, 
	awayteam INTEGER, 
	winner INTEGER DEFAULT NULL,
	PRIMARY KEY (week, hometeam, awayteam),
	FOREIGN KEY (hometeam) REFERENCES fantasyteams(teamid) ON DELETE CASCADE,
	FOREIGN KEY (awayteam) REFERENCES fantasyteams(teamid) ON DELETE CASCADE, 
	FOREIGN KEY (winner) REFERENCES fantasyteams(teamid) ON DELETE CASCADE
);

CREATE TABLE chats (
  id INT AUTO_INCREMENT,
  user VARCHAR(100),
  message VARCHAR(500),
	leagueid int,
  PRIMARY KEY(id), 
	FOREIGN KEY (leagueid) REFERENCES leagues(leagueid) ON DELETE CASCADE
);
	