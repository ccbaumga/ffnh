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
	ensure_logged_in();
	include("header.html"); 
	include("db.php");
	$pdo = $db;
	include("change_teamname_code.php");
	
	if (!isset($_SESSION['teamid'])){
		redirect("myteams.php");
	}
	
	
	$editFailed = [FALSE, ""];
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$newteamname = $_POST['teamname'];
		$teamid = $_SESSION['teamid'];
		$editFailed = change_teamname($newteamname, $teamid);
	} ?>
	
	<h1>Edit Team Name: <?php echo $_SESSION['teamname'];?></h1>
	<?php if ($editFailed[1] <> "") {
		?><p><?php echo $editFailed[1]; ?></p><?php
	} ?>
	<section class="form">
		<form id="settings" action="change_teamname.php" method="post">
			<div>
				<label for="teamname">Team Name:</label>
				<input type="text" name="teamname" id="teamname" value="<?php echo $_SESSION['teamname'];?>" >
			</div>
			<input type="submit" value="Change Team Name">
		</form>
	</section>
	<nav>
		<ul class="nav3">
			<li><a href="team.php">Back to My Team</a></li>
		</ul>
	</nav>
</body>
</html>