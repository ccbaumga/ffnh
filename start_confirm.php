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
	<?php
	include("session_handling.php");
	include("db.php");
	$pdo = $db;
	ensure_logged_in();
	include("header.html");
	include("start_creation.php");	

	$startFailed = FALSE;
	$startingLineupFull = checkStartingLineupFull();
	if($startingLineupFull === false && $_SERVER["REQUEST_METHOD"] == "GET"){
		$startteam = $_GET["nflteam"];
		$startinstance = $_GET["instance"];
		$error = start($startteam, $startinstance);
		$startFailed = TRUE;
		echo $error;
		die();
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$startteam = $_POST["startteam"];
		$startinstance = $_POST["startinstance"];
		$benchteam = $_POST["benchteam"];
		$error = startbench($startteam, $startinstance, $benchteam);
		$startFailed = TRUE;
		echo $error;
	} 
	?>
	<h1>Starting lineup full, in order to start <?php echo $_GET["instance"];?> <?php echo $_GET["nflteam"];?> you must bench a team. </h1>
	<section class="form">
		<form id="start" action="start_confirm.php" method="post">
			<div>
				<input type="hidden" id="startteam" name="startteam" value="<?php echo $_GET["nflteam"];?>">
				<input type="hidden" id="startinstance" name="startinstance" value="<?php echo $_GET["instance"];?>">
			</div>
			<div>
				<label for="benchteam">Team to Bench:</label>
				<select name="benchteam">
				<?php
				$statement = $pdo->prepare('select nflteam, instancenumber from nflteaminstances 
				where owner = ? and status = "starting"');
				$statement->execute([$_SESSION['teamid']]);
				$i = 0;
				while ($i < $startingLineupFull){
					$row = $statement->fetch();
					?>
					<option value="<?php echo $row['instancenumber'];?> <?php echo $row['nflteam'];?>">
					<?php echo $row['instancenumber'];?> <?php echo $row['nflteam'];?>
					</option>
					<?php
					$i = $i + 1;
				}
				?>
				</select>
			</div>
			
			<input type="submit" value="Start/Bench" >
		</form>
	</section>
	<h1>Nevermind, I don't want to start <?php echo $_GET["instance"];?> <?php echo $_GET["nflteam"];?>. </h1>
	<section class="form">
		<form id="add" action="team.php" method="get">
			<input type="submit" value="Back to my Team" >
		</form>
	</section>