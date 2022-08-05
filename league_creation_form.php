<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="league_creation_settings.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	include("db.php");
	ensure_logged_in();
	include("header.html");
	include("league_creation.php");	
	$creationFailed = FALSE;
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$leaguename = $_POST["leaguename"];
		$private = isset($_POST["private"]);
		$teamname = $_POST["teamname"];
		//$numinstances = $_POST["numinstances"];
		$teamid = 0;
		$leagueid = 0;
		$creationFailed = create_league($leaguename, $private, 
		$teamname);
		
		if (!$creationFailed) {
			echo "creation worked";
			//$_SESSION["username"] = $username;
			redirect("myteams.php");
		} 	
		
	} else {
		$leaguename = "";
		$private = false;
		$teamname = "";
		//$numinstances = "";
	}?>
	<h1>Create a New League</h1>
	<?php if ($creationFailed) {
		?><p><?php echo $creationFailed; ?></p><?php
	} ?>
	<section class="form">
		<form id="league" action="league_creation_form.php" method="post">
			<div>
				<label for="leaguename">League Name:</label>
				<input type="text" name="leaguename" id="leaguename" value="<?php echo $leaguename ?>">
			</div>
			<!--<div>
				<label for="numteams">Number of Teams:</label>
				<input type="text" name="numteams" id="numteams" value="<?php echo $numteams ?>">
			</div>-->
			<!--<div>
				<label for="numinstances">Number of Fantasy Instances per NFL Team (optional):</label>
				<input type="text" name="numinstances" id="numinstances" value="<?php echo $numinstances ?>">
			</div>-->
			<div>
				 <label for="private">Private League:</label>
				 <input type="checkbox" name="private" id="private" <?php if ($private) echo "checked" ?>>
			</div>
			<div>
				<label for="teamname">Your Fantasy Team Name (optional):</label>
				<input type="text" name="teamname" id="teamname" value="<?php echo $teamname ?>">
			</div>
			<input type="submit" value="Create" >
		</form>
	</section>