<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="gameinstance.css">
	<link rel="stylesheet" href="colors.css">
	<link rel="stylesheet" href="fteaminstance.css">
	<link rel="stylesheet" href="matchup.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	include("nav.html");
	include("gameinstance.php");
	include("fteaminstance.php");
	include("matchupfunction.php");
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	$currentweek = 1;
	$week = $currentweek;
	$currteamid = $teamid;
	if (isset($_GET['currteamid'])) {
		$currteamid = $_GET['currteamid'];
	}
	
	/*get opponents*/
	$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", "baumgc12", "mysql884812");
	$statement = $pdo->prepare('SELECT hometeam, awayteam 
	from fantasymatchups
	where hometeam = ?
	and week = ?
	or awayteam = ?
	and week = ?');
	$statement->execute([$currteamid, $week, $currteamid, $week]);
	$row = $statement->fetch();
	$hometeamid = $row['hometeam'];
	$awayteamid = $row['awayteam'];
	
	/*get fteam info and create objects*/
	$statement = $pdo->prepare('select teamid, teamname, wins, losses, ties, owner
	from fantasyteams
	where teamid = ?');
	$statement->execute([$awayteamid]);
	$fantasyteams = [];
	array_push($fantasyteams, $statement->fetch());
	$statement = $pdo->prepare('select teamid, teamname, wins, losses, ties, owner
	from fantasyteams
	where teamid = ?');
	$statement->execute([$hometeamid]);
	array_push($fantasyteams, $statement->fetch());
	$awayteam = new fteaminstance;
	fteamCreate($awayteam, $fantasyteams[0]);
	$hometeam = new fteaminstance;
	fteamCreate($hometeam, $fantasyteams[1]);
	
	/*get nflteaminstances*/
	$awaystartinggames = [];
	if ($pdo && $awayteam->teamid){
		formGames($pdo, $awayteam->teamid, 'starting', $awaystartinggames, $week);
	}
	$awaybenchgames = [];
	if ($pdo && $awayteam->teamid){
		formGames($pdo, $awayteam->teamid, 'bench', $awaybenchgames, $week);
	}
	$homestartinggames = [];
	if ($pdo && $hometeam->teamid){
		formGames($pdo, $hometeam->teamid, 'starting', $homestartinggames, $week);
	}
	$homebenchgames = [];
	if ($pdo && $hometeam->teamid){
		formGames($pdo, $hometeam->teamid, 'bench', $homebenchgames, $week);
	}
	
	
	
	?>
	<div id="week-selector">
		<?php if ($week > 1){
			?><button id="weekminus"> &lt </button> <?php
		} ?>
		<button> Week <?php echo $week;?></button>
		<?php if ($week < 17){
			?><button id="weekplus"> &gt </button> <?php
		} ?>
	</div>
	
	<div id="splitscreen">
		<div class="lefthalf half">
			<ul class="fteaminstance">
				<?php
					htmlSingleTeam($awayteam);
				 ?>
			</ul>
			<section class="starting roster">
				STARTING 
				<ul>
					<?php for ($i = 0; $i < sizeof($awaystartinggames); $i++) {
						htmlOfSingleGame($awaystartinggames[$i], 'matchup', $leagueid);
					} ?>
				</ul>
			</section>
			<section class="bench roster">
				BENCH 
				<ul>
					<?php for ($i = 0; $i < sizeof($awaybenchgames); $i++) {
						htmlOfSingleGame($awaybenchgames[$i], 'matchup', $leagueid);
					} ?>
				</ul>
			</section>
		</div>
		<div id="center"><!--this is the center-->
			<p>@</p>
		</div>
		<div class="righthalf half">
			<ul class="fteaminstance">
				<?php
					htmlSingleTeam($hometeam);
				 ?>
			</ul>
			<section class="starting roster">
				STARTING 
				<ul>
					<?php for ($i = 0; $i < sizeof($homestartinggames); $i++) {
						htmlOfSingleGame($homestartinggames[$i], 'matchup', $leagueid);
					} ?>
				</ul>
			</section>
			<section class="bench roster">
				BENCH 
				<ul>
					<?php for ($i = 0; $i < sizeof($homebenchgames); $i++) {
						htmlOfSingleGame($homebenchgames[$i], 'matchup', $leagueid);
					} ?>
				</ul>
			</section>
		</div>
	</div>