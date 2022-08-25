<?php //this code is called by add_team.php (add from admin) and join_league.php (join from league search)
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
	
	include("globalconstants.php");
	if (strlen($teamname) > $maxTeamname){
		return "Team name entered is more than " . $maxTeamname . " characters.<br>";
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
	
	/*check that league exists, and check if teams locked*/
	$statement = $pdo->prepare('select leagueid, teamslocked
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
	if ($extrastatements['teamslocked'] <> 0){
		return "Teams are already locked for this league";
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
	/*confirm team creation*/
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
	
	//insert into chats
	$statement = $pdo->prepare('insert into chats
	(user, message, leagueid) values 
	(?, ?, ?)');
	$statement->execute(["ADMIN", $teamname . " (" . $username . ") has joined the league", $leagueid]);
	
	return "";
}