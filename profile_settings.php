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
	include("db.php");
	$pdo = $db;
	include("profile_change.php");
	
	
	$editFailed = [FALSE, ""];
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$profilename = $_POST['username'];
		$editFailed = change_profile($profilename);
	} ?>
	
	<h1>Edit Profile: <?php echo $_SESSION['username'];?></h1>
	<?php if ($editFailed[1] <> "") {
		?><p><?php echo $editFailed[1]; ?></p><?php
	} ?>
	<section class="form">
		<form id="settings" action="profile_settings.php" method="post">
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="<?php echo $_SESSION['username'];?>" >
			</div>
			<input type="submit" value="Change Profile">
		</form>
	</section>
	<nav>
		<ul class="nav3">
			<li><a href="myteams.php">Back to My Teams</a></li>
		</ul>
	</nav>
</body>
</html>