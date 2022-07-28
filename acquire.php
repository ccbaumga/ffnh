<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="acquire.css">
	<link rel="stylesheet" href="gameinstance.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	include("nav.html");
	include("gameinstance.php");
	//$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", "baumgc12", "mysql884812");
	include("db.php");
	$pdo = $db;
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	$currentweek = 1;
	$week = $currentweek;
	
	/*get available teams*/
	$statement = $pdo->prepare('SELECT i.nflteam, i.instancenumber, i.status, 
	n.location, n.wins, n.losses, n.ties
	from nflteaminstances i
	join nflteams n on n.abbr = i.nflteam
	where owner IS NULL
	and league = ?');
	$statement->execute([$leagueid]);
	
	$available = [];
	$numavailableteams = 0;
	while ($row = $statement->fetch()) {
		$numavailableteams = array_push($available, $row);
	}
	
	/*create game objects*/
	$games = [];
	for ($i = 0; $i < $numavailableteams; $i++) {
		array_push($games, new gameinstance);
		gameCreate($games[$i], $available[$i], $pdo, $week);
	}
	?>
	<section class="starting roster">
		AVAILABLE 
		<ul>
			<?php for ($i = 0; $i < $numavailableteams; $i++) {
				htmlOfSingleGame($games[$i], "", $leagueid);
			} ?>
		</ul>
	</section>
</body>
</html>