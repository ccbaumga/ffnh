DROP TABLE IF EXISTS history;
DROP TABLE IF EXISTS chats;
DROP TABLE IF EXISTS nflteaminstances;
DROP TABLE IF EXISTS fantasymatchups;
DROP TABLE IF EXISTS fantasyteams;
DROP TABLE IF EXISTS nflgames;
DROP TABLE IF EXISTS nflteams;
DROP TABLE IF EXISTS leagues;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS globals;

CREATE TABLE globals(
  currentweek INTEGER, 
  urlpath VARCHAR(100),
  lastscoreupdate DATETIME,
  updateinprocess BOOLEAN DEFAULT FALSE, 
  updatetimedelta TIME DEFAULT '00:01:00'
);

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
	teamslocked BOOLEAN DEFAULT false,  
	privacy ENUM ('public', 'private') DEFAULT 'public', 
	rosterlimit INTEGER DEFAULT NULL, 
	maxstart INTEGER DEFAULT NULL, 
	drafttime DATETIME DEFAULT NULL, 
	regularweeks SET ('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', 'wildcard', 'divisional', 'conference', 'superbowl') DEFAULT '1,15',
	playoffweeks SET ('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', 'wildcard', 'divisional', 'conference', 'superbowl') DEFAULT '16,17',
	playoffteams INTEGER DEFAULT 4,
	standingstiebreaker ENUM ('h2h', 'points') DEFAULT 'h2h',
	weeklytiebreaker ENUM ('home', 'ties') DEFAULT 'home',
	tiesetting ENUM ('0', 'rounddown', 'roundup') DEFAULT 'rounddown',
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
    autostartlineup BOOLEAN DEFAULT false,
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

CREATE TABLE history (
    nflteam VARCHAR(3), 
	league INTEGER, 
	instancenumber INTEGER,
	week INTEGER, 
	owner INTEGER, 
	status ENUM ('starting', 'bench') DEFAULT NULL,
	PRIMARY KEY (nflteam, league, instancenumber, week),
	FOREIGN KEY (nflteam) REFERENCES nflteams(abbr) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (league) REFERENCES leagues(leagueid) ON DELETE CASCADE,
	FOREIGN KEY (owner) REFERENCES fantasyteams(teamid) ON DELETE CASCADE
);