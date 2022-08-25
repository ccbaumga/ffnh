<?php
function remove_team($removeteam){
	$pdo = db_connect();
	$statement = $pdo->prepare('select leaguename, admin, teamslocked from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		return "No such league (" . $_SESSION['leagueid'] . "). <br>";
	}
	if ($_SESSION['username'] <> $row['admin']){
		return "You (" . $_SESSION['username'] . ") are not the admin (" . $row['admin'] . ") of this league (" . $_SESSION['leagueid'] . ").";
	}
	if ($row['teamslocked'] <> 0){
		return "This league (" . $_SESSION['leagueid'] . ") has locked teams. Cannot remove a team. <br>";
	}
	$statement = $pdo->prepare('select league from fantasyteams
	where teamid = ?');
	$statement->execute([$removeteam]);
	$row = $statement->fetch();
	if ($row == false){
		return "No such team (" . $removeteam . "). <br>";
	}
	if ($row['league'] <> $_SESSION['leagueid']){
		return "This team (" . $removeteam . ") is not in this league (" . $_SESSION['leagueid'] . "). <br>";
	}
	$statement = $pdo->prepare('delete from fantasyteams
	where teamid = ?');
	$statement->execute([$removeteam]);
	$statement = $pdo->prepare('insert into chats (user, message, leagueid) VALUES (?, ?, ?)');
	$statement->execute(["ADMIN", "(" . $_SESSION['username'] . ") has removed a team from league", $_SESSION['leagueid']]);
	redirect("standings.php");
	return false;
}
?>