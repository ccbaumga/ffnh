<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="league.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	include("db.php");
	ensure_logged_in();
	include("header.html");
	include("nav.html");
	include("create_fteam_schedule.php");
	
	$lockFailed = FALSE;
	if(isset($_GET['att'])) {
		$lockFailed = lock_teams($_SESSION['leagueid']);
		if ($lockFailed === false){
			redirect("admin_tools.php");
		}
	} ?>
		
	<?php if ($lockFailed){
		echo $lockFailed;
	}?>
	<nav>
		<ul class="nav4">
			<li><a href="lock_teams.php?att=1">Lock Teams</a></li>
		</ul>
	</nav>