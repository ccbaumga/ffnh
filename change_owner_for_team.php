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
	include("change_team_owner_creation.php");
	
	
	$editFailed = [FALSE, ""];
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$newusername = $_POST['username'];
		$league = $_POST['league'];
		$teamid = $_POST['teamid'];
		$editFailed = change_team_owner($newusername, $league, $teamid);
	} 
	
	if (isset($_GET['changeteam'])){
		$teamid = $_GET['changeteam'];
	}
	
	if (isset($teamid)){
		
		$statement = $pdo->prepare('select teamname, owner, league from fantasyteams
		where teamid = ?');
		$statement->execute([$teamid]);
		$row = $statement->fetch();
		if ($row === false){
			echo "Team selected (" . $teamid . ") does not exist. <br>";
		} else {
		
		?>
		
		<h1>Edit Team Owner: <?php echo $row['teamname'];?></h1>
		<?php if ($editFailed[1] <> "") {
			?><p><?php echo $editFailed[1]; ?></p><?php
		} ?>
		<section class="form">
			<form id="settings" action="change_owner_for_team.php" method="post">
				<table>
					<tr>
						<th>Teamname</th>
						<th>Username</th>
					</tr>
					<tr>
						<td><?php echo $row['teamname'];?></td>
						<td><input type="text" name="username" id="username" value="<?php echo $row['owner'];?>" ></td>
				</table>
				<input type="hidden" name="league" id="league" value="<?php echo $row['league'];?>" >
				<input type="hidden" name="teamid" id="teamid" value="<?php echo $teamid;?>" >
				<input type="submit" value="Change Owner">
			</form>
		</section>
		<?php }
	} else {
		echo "No Team Selected <br>";
	}?>
	<nav>
		<ul class="nav3">
			<li><a href="standings.php">Cancel Change Owner / Back to Standings</a></li>
		</ul>
	</nav>
</body>
</html>