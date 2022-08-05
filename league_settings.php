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
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	include("db.php");
	$pdo = $db;
	$statement = $pdo->prepare('select currentweek from globals');
	$statement->execute([]);
	$row = $statement->fetch();
	$currentweek = $row['currentweek'];
	$week = $currentweek;
	
	$statement = $pdo->prepare('select count(*) as numteams from fantasyteams
	where league = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	$numteams = $row['numteams'];
	$statement = $pdo->prepare('select count(*) as maxinstances from nflteaminstances
	where league = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	$maxinstances = $row['maxinstances'];
	$remainder = $maxinstances % 32;
	if ($remainder == 0){
		$maxinstances = $maxinstances / 32;
	} else {
		$maxinstances = "Not Divisible";
	}
	$statement = $pdo->prepare('select admin, teamslocked, privacy, rosterlimit, maxstart, 
	drafttime, regularweeks, playoffweeks, playoffteams, standingstiebreaker, weeklytiebreaker, tiesetting from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	?>
	<h2>League Settings</h2>
	<table id="league_settings_table">
	  <tr>
		<th>League Info</th>
	  </tr>
	  <tr>
		<td>League Name:</td>
		<td><?php echo $_SESSION['leaguename'];?></td>
	  </tr>
	  <tr>
		<td>League ID:</td>
		<td><?php echo $_SESSION['leagueid'];?></td>
	  </tr>
	  <tr>
		<td>League Commissioner:</td>
		<td><?php echo $row['admin'];?></td>
	  </tr>
	  <tr>
		<th>General Settings</th>
	  </tr>
	  <tr>
		<td>Privacy:</td>
		<td><?php echo $row['privacy'];?></td>
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
	  </tr>
	  <tr>
		<td>Draft Date:</td>
		<td> <?php
		if (($row['drafttime'] < date("Y-m-d H:i:s")) && (!is_null($row['drafttime']))){
			echo "Already Drafted";
		} else {
			echo $row['drafttime'];
		}
		?></td>
	  </tr>
	  <tr>
		<th>Roster Settings</th>
	  </tr>
	  <tr>
		<td>Number of Each NFL Team:</td>
		<td><?php echo $maxinstances;?></td>
	  </tr>
	  <tr>
		<td>Maximum Roster Size:</td>
		<td><?php echo $row['rosterlimit'];?></td>
	  </tr>
	  <tr>
		<td>Starting Lineup Size:</td>
		<td><?php echo $row['maxstart'];?></td>
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
		<td><?php echo $row['playoffteams'];?></td>
	  </tr>
	  <tr>
		<td>Playoff Weeks:</td>
		<td><?php echo $row['playoffweeks'];?></td>
	  </tr>
	  <tr>
		<td>Standings Tiebreaker:</td>
		<td><?php
		if ($row['standingstiebreaker'] == "h2h"){
			echo "Head to Head, then Total Points";
		} else {
			echo "Total Points, then Head to Head";
		}
		?></td>
	  </tr>
	  <tr>
		<td>Weekly Tiebreaker:</td>
		<td><?php
		if ($row['weeklytiebreaker'] == "home"){
			echo "Home Team Wins Ties";
		} else {
			echo "Allow Ties";
		}
		?></td>
	  </tr>
	</table>