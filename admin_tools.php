<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="admin_tools.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("db.php");
	include("header.html"); 
	include("nav.html");
	$pdo = db_connect();
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	if (!isset($_SESSION["leagueadmin"])) {
		redirect("team.php");
	}
	$statement = $pdo->prepare('SELECT teamslocked, drafttime
		from leagues
		where leagueid = ?');
		$statement->execute([$_SESSION['leagueid']]);
		$row = $statement->fetch();
		$teamslocked = $row['teamslocked'];
		$drafttime = $row['drafttime'];
	?>
	<nav>
		<ul class="adminbutton">
			<li><a href="edit_settings.php">Edit League Settings</a></li>
		</ul>
	</nav>
	<?php if ($teamslocked == 0 && ($drafttime > date("Y-m-d H:i:s") || is_null($drafttime))) { ?>
	<nav>
		<ul class="adminbutton">
			<li><a href="add_team.php">Add a Team by Username</a></li>
		</ul>
	</nav>
	<nav>
		<ul class="adminbutton">
			<li><a href="remove_team.php">Remove a Team</a></li>
		</ul>
	</nav>
	<nav>
		<ul class="adminbutton">
			<li><a href="lock_teams.php">Lock Teams</a></li>
		</ul>
	</nav>
	<?php } ?>
	<?php if ($teamslocked == 1 && ($drafttime > date("Y-m-d H:i:s") || is_null($drafttime))) { ?>
	<nav>
		<ul class="adminbutton">
			<li><a href="set_draft_time.php">Set Draft Time</a></li>
		</ul>
	</nav>
	<nav>
		<ul class="adminbutton">
			<li><a href="unlock_teams.php">Unlock Teams</a></li>
		</ul>
	</nav>
	<?php } ?>
	<nav>
		<ul class="adminbutton">
			<li><a href="change_team_owner.php">Edit Team Owner</a></li>
		</ul>
	</nav>
	