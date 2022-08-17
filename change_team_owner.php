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
	<?php
	include("session_handling.php");
	include("db.php");
	$pdo = $db;
	ensure_logged_in();
	include("header.html");
	?>
	<h1>Change a Team Owner in League: <?php echo $_SESSION['leaguename'];?></h1>
	<section class="form">
		<form id="change" action="change_owner_for_team.php" method="get">
			<div>
				<label for="changeteam">Fantasy Team to Change Owner:</label>
				<select name="changeteam">
				<?php
				$statement = $pdo->prepare('select teamid, owner, teamname, teamimage from fantasyteams
				where league = ?');
				$statement->execute([$_SESSION['leagueid']]);
				$i = 0;
				while ($row = $statement->fetch()){
					?>
					<option value="<?php echo $row['teamid'];?>">
					<?php echo $row['teamname'];?> (<?php echo $row['owner'];?>)
					</option>
					<?php
				}
				?>
				</select>
			</div>
			
			<input type="submit" value="Select Team" >
		</form>
	</section>
	<nav>
		<ul class="nav3">
			<li><a href="admin_tools.php">Cancel Change Owner</a></li>
		</ul>
	</nav>