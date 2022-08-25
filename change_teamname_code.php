<?php
function change_teamname($teamname, $teamid){
	$error = [true, ""];
	$teamname = trim($teamname);
	if ($teamname == ""){
		return [true, "New teamname cannot be blank/whitespace<br>"];
	}
	$pdo = db_connect();
	$statement = $pdo->prepare('select owner, league, teamname from fantasyteams where teamid = ?');
	$statement->execute([$teamid]);
	$row = $statement->fetch();
	if ($row === false){
		return [true, "Your current teamid (" . $teamid . ") does not exist. <br>"];
	}
	if ($teamname == $row['teamname']){
		return [false, ""];
	}
	if ($row['owner'] <> $_SESSION['username']){
		return [true, "You (" . $_SESSION['username'] . ") are not the owner (" . $row['owner'] . ") of this team (" . $teamid . ", " . $row['teamname'] . ").<br>"];
	}
	include("globalconstants.php");
	if (strlen($teamname) > $maxTeamname){
		return [true, "Team name entered is more than " . $maxTeamname . " characters.<br>"];
	}
	$statement = $pdo->prepare('update fantasyteams set teamname = ? where teamid = ?');
	$statement->execute([$teamname, $teamid]);
	$_SESSION['teamname'] = $teamname;
	
	$statement = $pdo->prepare('insert into chats (user, message, leagueid) VALUES (?, ?, ?)');
	$statement->execute(["ADMIN", "(" . $_SESSION['username'] . ") has changed their teamname from (" . $row['teamname'] . ") to (" . $teamname . ")", $row['league']]);
	
	return [false, "Successfully changed teamname from (" . $row['teamname'] . ") to (" . $teamname . "). <br>"];
	
}

?>