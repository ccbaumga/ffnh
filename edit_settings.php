<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="league.css">
	<link rel="stylesheet" href="fteaminstance.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	include("nav.html");
	include("league_creation.php");
	include("db.php");
	$pdo = $db;
	
	$editFailed = [FALSE, ""];
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$newsettings = new settingsball;
		$newsettings->leaguename = $_POST["leaguename"];
		$newsettings->private = isset($_POST["private"]);
		$newsettings->numinstances = $_POST["numinstances"];
		$newsettings->rostersize = $_POST["rostersize"];
		$newsettings->startingsize = $_POST["startingsize"];
		$newsettings->playoffteams = $_POST["playoffteams"];
		$newsettings->playoffweeks = $_POST["playoffweeks"];
		$newsettings->standingstiebreaker = $_POST["standingstiebreaker"];
		$newsettings->weeklytiebreaker = $_POST["weeklytiebreaker"];
		$editFailed = editsettings($newsettings, $pdo);

		
	} else {
		/*$leaguename = "";
		$private = false;
		$teamname = "";
		$numinstances = "";*/
	}
	
	$statement = $pdo->prepare('select count(*) as numteams from fantasyteams
	where league = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	$numteams = $row['numteams'];
	
	$statement = $pdo->prepare('select currentweek from globals');
	$statement->execute([]);
	$row = $statement->fetch();
	$currentweek = $row['currentweek'];
	
	$statement = $pdo->prepare('select count(*) as maxinstances from nflteaminstances
	where league = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	$maxinstances = $row['maxinstances'];
	$remainder = $maxinstances % 32;
	if ($remainder == 0){
		$maxinstances = $maxinstances / 32;
	} else {
		$maxinstances = "Not Divisible";
	}
	
	$statement = $pdo->prepare('select leaguename, admin, teamslocked, privacy, rosterlimit, maxstart, 
	drafttime, regularweeks, playoffweeks, playoffteams, standingstiebreaker, weeklytiebreaker, tiesetting from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	$_SESSION['leaguename'] = $row['leaguename'];
	?>
	
	<h1>Edit League Settings</h1>
	<?php if ($editFailed[1] <> "") {
		?><p><?php echo $editFailed[1]; ?></p><?php
	} ?>
	<section class="form">
		<form id="settings" action="edit_settings.php" method="post">
			<table>
				<tr>
					<th>Field</th>
					<th>Value</th>
					<th>Change</th>
				<tr>
					<td>League Name:</td>
					<td><input type="text" name="leaguename" id="leaguename" value="<?php echo $_SESSION['leaguename'] ?>" maxlength="<?php echo $maxLeaguename;?>"></td>
				</tr>
				<tr>
					<td>League ID:</td>
					<td><?php echo $_SESSION['leagueid'];?></td>
			    </tr>
			    <tr>
				    <td>League Commissioner:</td>
				    <td><?php echo $row['admin'];?></td>
					<td>(Go to admin_tools.php and click Change Commissioner to change)</td>
			    </tr>
				<tr>
					<th>General Settings</th>
				</tr>
				<tr>
					<td>Private League:</td>
					<td><input type="checkbox" name="private" id="private" <?php if ($row['privacy'] == "private") echo "checked" ?>></td>
				</tr>
				<tr>
					<td>Number of Fantasy Teams:</td>
					<td><?php echo $numteams;?></td>
				</tr>
				<tr>
					<td>Fantasy Teams Locked?:</td>
					<td><?php 
					if ($row['teamslocked']){
						echo "Locked";
					} else {
						echo "No";
					}
					?></td>
					<td>(Go to admin_tools.php and click Lock/Unlock Teams to change)</td>
				</tr>
				<tr>
					<td>Draft Date:</td>
					<td> <?php
					if ($row['drafttime'] < date("Y-m-d H:i:s")){
						echo "Already Drafted";
					} else {
						echo $row['drafttime'];
					}
					?></td>
					<td>(Go to admin_tools.php and click Set Draft Time to change)</td>
				</tr>
				<tr>
					<th>Roster Settings</th>
				</tr>
				<tr>
					<td>Number of Each NFL Team (0-<?php echo $maxNumInstances?>):</td>
					<td><input type="number" name="numinstances" id="numinstances" min="0" max="<?php echo $maxNumInstances?>" value="<?php echo $maxinstances;?>"></td>
				</tr>
				<tr>
					<td>Maximum Roster Size:</td>
					<td><input type="number" name="rostersize" id="rostersize" min="0" value="<?php echo $row['rosterlimit'];?>"></td>
				</tr>
				<tr>
					<td>Starting Lineup Size:</td>
					<td><input type="number" name="startingsize" id="startingsize" min="0" value="<?php echo $row['maxstart'];?>"></td>
				</tr>
				<tr>
					<td>Starting Lineup Lock:</td>
					<td>Start of Each Game</td>
				</tr>
				<tr>
					<td>Starting Lineup Unlock:</td>
					<td>End of the Last Game of the Week</td>
				</tr>
				<tr>
					<th>Playoff Settings</th>
				</tr>
				<tr>
					<td>Current Week:</td>
					<td><?php echo $currentweek;?></td>
				</tr>
				<tr>
					<td>Number of Playoff Teams:</td>
					<td><input type="number" name="playoffteams" id="playoffteams" min="0" value="<?php echo $row['playoffteams'];?>"></td>
				</tr>
				<tr>
					<td>Playoff Weeks:</td>
					<td><input type="text" name="playoffweeks" id="playoffweeks" value="<?php echo $row['playoffweeks'];?>"></td>
				</tr>
				<tr>
					<td>Standings Tiebreaker:</td>
					<td>
						<select name="standingstiebreaker" id="standingstiebreaker">
							<option value="h2h"
								<?php
								if ($row['standingstiebreaker'] == "h2h"){
									echo "selected";
								}
								?>
								>Head to Head, then Total Points</option>
							<option value="points"
								<?php
								if ($row['standingstiebreaker'] == "points"){
									echo "selected";
								}
								?>
								>Total Points, then Head to Head</option>
					</td>
				</tr>
				<tr>
					<td>Weekly Tiebreaker:</td>
					<td>
						<select name="weeklytiebreaker" id="weeklytiebreaker">
							<option value="home"
								<?php
								if ($row['weeklytiebreaker'] == "home"){
									echo "selected";
								}
								?>
								>Home Team Wins if Tied</option>
							<option value="ties"
								<?php
								if ($row['weeklytiebreaker'] == "ties"){
									echo "selected";
								}
								?>
								>Allow Ties</option>
					</td>
				</tr>
			</table>
			<input type="submit" value="Update Settings" >
		</form>
	</section>
