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
	include("nav.html");
	include("team_creation.php");
	
	$creationFailed = FALSE;
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$username = $_POST['username'];
		$teamname = $_POST['teamname'];
		$creationFailed = create_team($_SESSION['leagueid'], $_POST['username'], 
		$_POST['teamname']);
		
		if (!$creationFailed) {
			echo "creation worked";
			redirect("standings.php");
		} 	
		
	} else {
		$username = "";
		$teamname = "";
	}?>
	<h1>Add a Team to League: <?php echo $_SESSION['leaguename'];?></h1>
	<?php if ($creationFailed) {
		?><p><?php echo $creationFailed; ?></p><?php
	} ?>
	<section class="form">
		<form id="league" action="add_team.php" method="post">
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="<?php echo $username ?>">
			</div>
			<div>
				<label for="teamname">Fantasy Team Name (optional):</label>
				<input type="text" name="teamname" id="teamname" value="<?php echo $teamname ?>">
			</div>
			<input type="submit" value="Add" >
		</form>
	</section>