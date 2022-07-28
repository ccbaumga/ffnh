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
	include("add_creation.php");	

	$addFailed = FALSE;
	$rosterFull = checkRosterFull();
	if($rosterFull === false && $_SERVER["REQUEST_METHOD"] == "GET"){
		$addteam = $_GET["nflteam"];
		$addinstance = $_GET["instance"];
		$error = add($addteam, $addinstance);
		$addFailed = TRUE;
		echo $error;
		die();
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$addteam = $_POST["addteam"];
		$addinstance = $_POST["addinstance"];
		$dropteam = $_POST["dropteam"];
		$error = adddrop($addteam, $addinstance, $dropteam);
		$addFailed = TRUE;
		echo $error;
	} 
	/*$rosterFull = FALSE;
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$addteam = $_GET["nflteam"];
		$addinstance = $_GET["instance"];
		if (add($addteam, $addinstance)) {
			redirect("team.php");
		} else {
			$rosterFull = TRUE;
		}
	}*/
		?>
	<h1>Roster full, in order to add <?php echo $_GET["instance"];?> <?php echo $_GET["nflteam"];?> you must drop a team. </h1>
	<section class="form">
		<form id="add" action="add_confirm.php" method="post">
			<div>
				<input type="hidden" id="addteam" name="addteam" value="<?php echo $_GET["nflteam"];?>">
				<input type="hidden" id="addinstance" name="addinstance" value="<?php echo $_GET["instance"];?>">
			</div>
			<div>
				<label for="dropteam">Team to Drop:</label>
				<select name="dropteam">
				<?php
				$statement = $pdo->prepare('select nflteam, instancenumber from nflteaminstances 
				where owner = ?');
				$statement->execute([$_SESSION['teamid']]);
				$i = 0;
				while ($i < $rosterFull){
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
			
			<input type="submit" value="Add/Drop" >
		</form>
	</section>
	<h1>Nevermind, I don't want to add <?php echo $_GET["instance"];?> <?php echo $_GET["nflteam"];?>. </h1>
	<section class="form">
		<form id="add" action="team.php" method="get">
			<input type="submit" value="Back to my Team" >
		</form>
	</section>