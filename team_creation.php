<?php
function create_team($leagueid, $username, $teamname, $allowduplicates = true) {
	
	/*check for simple errors*/
	$username = trim($username);
	if (!$username) {
		return "Please enter a username. " . "<br>";
	}
	
	$teamname = trim($teamname);
	if (!$teamname) {
		$teamname = $username;
	}
	
	/*check that username exists*/
	$pdo = db_connect();
	$statement = $pdo->prepare('select username
	from profiles
	where username = ?');
	$statement->execute([$username]);
	$numrows = 0;
	while ($extrastatements = $statement->fetch()) {
		$numrows++;
	}
	if ($numrows != 1) {
		return "Username does not exist";
	}
	
	/*check that league exists*/
	$statement = $pdo->prepare('select leagueid
	from leagues
	where leagueid = ?');
	$statement->execute([$leagueid]);
	$numrows = 0;
	while ($extrastatements = $statement->fetch()) {
		$numrows++;
	}
	if ($numrows != 1) {
		return "League does not exist";
	}
	
	/*check for duplicate profile in league*/
	if ($allowduplicates == false) {
		
	}
	/*check for duplicate teamname in league*/
	if ($allowduplicates == false) {
		
	}
	
	/*insert into fantasyteams*/
	$statement = $pdo->prepare('insert into fantasyteams
	(owner, league, teamname) values 
	(?, ?, ?)');
	$statement->execute([$username, $leagueid, $teamname]);
	/*confirm league creation*/
	$statement = $pdo->prepare('select last_insert_id()');
	$statement->execute([]);
	$row = $statement->fetch();
	$extra = 0;
	while ($extrastatements = $statement->fetch()) {
		$extra++;
	}
	if ($extra != 0) {
		return "Extra last_insert_id statements";
	}
	if ($row[0] === 0) {
		return "Last_insert_id returns 0";
	}
	$teamid = $row[0];
	echo "teamid : " . $teamid;
	if ($teamid == null) {
		return "teamid is null";
	}
	
	return "";
}