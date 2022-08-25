<?php
function change_team_owner($newusername, $league, $teamid){
	$pdo = db_connect();
	$statement = $pdo->prepare('select admin, leaguename from leagues
	where leagueid = ?');
	$statement->execute([$league]);
	$row = $statement->fetch();
	if ($row === false){
		return [true, "League (" . $league . ") does not exist. <br>"];
	}
	if ($row['admin'] <> $_SESSION['username']){
		return [true, "You (" . $_SESSION['username'] . ") are not the admin (" . $row['admin'] . ") of this league (" . $leagueid . ", " . $row['leaguename'] . ")."];
	}
	$statement = $pdo->prepare('select owner, league, teamname from fantasyteams
	where teamid = ?');
	$statement->execute([$teamid]);
	$row = $statement->fetch();
	if ($row === false){
		return [true, "Team (" . $teamid . ") does not exist. <br>"];
	}
	if ($row['league'] <> $league){
		return [true, "League (" . $row['league'] . ") of team (" . $teamid . ", " . $row['teamname'] . ") does not equal given league (" . $league . ").<br>"];
	}
	$teamname = $row['teamname'];
	if ($newusername == $row['owner']){
		return [false, ""];
	}
	$oldowner = $row['owner'];
	$statement = $pdo->prepare('select username from profiles
	where username = ?');
	$statement->execute([$newusername]);
	$row = $statement->fetch();
	if ($row === false){
		return [true, "Given username (" . $newusername . ") does not exist in database.<br>"];
	}
	$statement = $pdo->prepare('update fantasyteams set owner = ?
	where teamid = ?');
	$statement->execute([$newusername, $teamid]);
	
	$statement = $pdo->prepare('insert into chats (user, message, leagueid) VALUES (?, ?, ?)');
	$statement->execute(["ADMIN", "(" . $_SESSION['username'] . ") has changed the owner of a team", $_SESSION['leagueid']]);
	
	return [false, "Owner of team (" . $teamid . ", " . $teamname . ") changed from (" . $oldowner . ") to (" . $newusername . ").<br>"];
}
?>