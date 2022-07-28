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
	<script src="standings.js" defer></script>
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	include("nav.html");
	include("fteaminstance.php");
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	$currentweek = 1;
	$week = $currentweek;
	
	/*get all fantasy teams*/
	//$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", "baumgc12", "mysql884812");
	include("db.php");
	$pdo = $db;
	$statement = $pdo->prepare('select teamid, teamname, wins, losses, ties, owner
	from fantasyteams
	where league = ?
	order by wins');
	$statement->execute([$leagueid]);
	$fantasyteams = [];
	$numteams = 0;
	while ($row = $statement->fetch()) {
		$numteams = array_push($fantasyteams, $row);
	}
	
	/*create fteam objects*/
	$fteams = [];
	for ($i = 0; $i < $numteams; $i++) {
		array_push($fteams, new fteaminstance);
		fteamCreate($fteams[$i], $fantasyteams[$i]);
	} 
	
	/*see draft time*/
	if($currentweek == 0) {
		$statement = $pdo->prepare('SELECT drafttime
		from leagues
		where leagueid = ?');
		$statement->execute([$leagueid]);
		$row = $statement->fetch();
		$drafttime = $row[0];
	}
	?>
	
	<section id="standings">
		<h1 id="leaguename"> <?php echo $leaguename ?> </h1>
		<ul class="fteaminstance">
			<?php for ($i = 0; $i < $numteams; $i++) {
				htmlSingleTeam($fteams[$i]);
			} ?>
		</ul>
		<?php if ($currentweek == 0) {
		?><span>Draft is set for: <?php echo $drafttime;?></span><?php
		}?>
	</section>
	<nav>
		<ul class="nav3">
			<li><a href="league_settings.html">League Settings</a></li>
		</ul>
	</nav>
	<?php if (isset($_SESSION['leagueadmin'])) { ?>
	<nav>
		<ul class="nav4">
			<li><a href="admin_tools.php">Admin Tools</a></li>
		</ul>
	</nav>
	<?php } ?>
</body>
</html>
	