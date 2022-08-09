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
	include("team_creation.php");
	
	$creationFailed = FALSE;
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$username = $_SESSION['username'];
		$teamname = trim($_POST['teamname']);
		$addleague = trim($_POST['addleague']);
		$creationFailed = create_team($addleague, $username, 
		$teamname);
		
		if (!$creationFailed) {
			echo "creation worked";
			redirect("myteams.php");
		} 	
		
	} else {
		$username = "";
		$teamname = "";
		$addleague = $_GET['search'];
	}
	$pdo = db_connect();
	$statement = $pdo->prepare('select leaguename from leagues where leagueid = ?');
	$statement->execute([$addleague]);
	$row = $statement->fetch();
	?>
	<h1>Join This League: <?php echo $row['leaguename'];?></h1>
	<?php if ($creationFailed) {
		?><p><?php echo $creationFailed; ?></p><?php
	} ?>
	<section class="form">
		<form id="league" action="join_league.php" method="post">
			<input type="hidden" id="addleague" name="addleague" value="<?php echo $addleague;?>">
			<div>
				<label for="teamname">Fantasy Team Name:</label>
				<input type="text" name="teamname" id="teamname" value="<?php echo $teamname ?>">
			</div>
			<input type="submit" value="Add" >
		</form>
	</section>
	