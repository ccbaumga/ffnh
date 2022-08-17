<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="team.css">
	<link rel="stylesheet" href="gameinstance.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("db.php");
	include("header.html"); 
	include("nav.html");
	include("gameinstance.php");
	//include("globalconstants.php");
	$maxweek = 18;
	$pdo = db_connect();
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	if (isset($_GET['teamid'])) {
		$_SESSION['teamid'] = $_GET['teamid'];
		$statement = $pdo->prepare('SELECT f.teamname, f.teamimage, f.league, l.leaguename, 
		l.admin
		from fantasyteams f
		join leagues l on f.league = l.leagueid
		where teamid = ?');
		$statement->execute([$_SESSION['teamid']]);
		$row = $statement->fetch();
		$_SESSION['teamname'] = $row[0];
		$_SESSION['leagueid'] = $row[2];
		$_SESSION['leaguename'] = $row[3];
		if ($username == $row[4]) {
			$_SESSION['leagueadmin'] = true;
		} else if (isset($_SESSION['leagueadmin'])) {
			unset($_SESSION['leagueadmin']);
		}
	}
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	if (isset($_GET['otherteamid'])) {
		$teamid = $_GET['otherteamid'];
		$statement = $pdo->prepare('SELECT teamname, owner, teamimage, wins, losses, ties
		from fantasyteams
		where teamid = ?');
		$statement->execute([$teamid]);
		$row = $statement->fetch();
		$teamname = $row[0];
		$username = $row[1];
		//others
	}
	$currentweek = 1;
	$week = $currentweek;
	$draftcomplete = true;
	if ($currentweek == 0){
		$draftcomplete = false;
	}
	
	/*get starting teams*/
	$statement = $pdo->prepare('SELECT i.nflteam, i.instancenumber, i.status, 
	n.location, n.wins, n.losses, n.ties
	from nflteaminstances i
	join nflteams n on n.abbr = i.nflteam
	where owner = ?
	and status = ?');
	$statement->execute([$teamid, 'starting']);
	
	$starting = [];
	$numstartingteams = 0;
	while ($row = $statement->fetch()) {
		$numstartingteams = array_push($starting, $row);
	}
	
	/*create game objects*/
	$games = [];
	for ($i = 0; $i < $numstartingteams; $i++) {
		array_push($games, new gameinstance);
		gameCreate($games[$i], $starting[$i], $pdo, $week);
	}
	
	/*get bench teams*/
	$statement = $pdo->prepare('SELECT i.nflteam, i.instancenumber, i.status, 
	n.location, n.wins, n.losses, n.ties
	from nflteaminstances i
	join nflteams n on n.abbr = i.nflteam
	where owner = ?
	and status = ?');
	$statement->execute([$teamid, 'bench']);
	$bench = [];
	$numbenchteams = 0;
	while ($row = $statement->fetch()) {
		$numbenchteams = array_push($bench, $row);
	}
	
	/*create game objects*/
	$benchgames = [];
	for ($i = 0; $i < $numbenchteams; $i++) {
		array_push($benchgames, new gameinstance);
		gameCreate($benchgames[$i], $bench[$i], $pdo, $week);
	}
	
	/*see draft time*/
	if($draftcomplete == false) {
		$statement = $pdo->prepare('SELECT drafttime
		from leagues
		where leagueid = ?');
		$statement->execute([$leagueid]);
		$row = $statement->fetch();
		$drafttime = $row[0];
	}
	?>
	<?php /*
	<div id="week-selector">
		<?php if ($week > 1){
			?><button id="weekminus"> &lt </button> <?php
		} ?>
		<button> Week <?php echo $week;?></button>
		<?php if ($week < $maxweek){
			?><button id="weekplus"> &gt </button> <?php
		} ?>
		<!--<select name="week">
			<option value="1">Week 1</option>
			<option value="2">Week 2</option>
		</select>-->
	</div> */ ?>
	<section class="teamname">
		<h1>
			<img src="ffnh.png" alt="Team Image" style="float:left;width:42px;height:42px;">
			<?php echo $teamname;?>
		</h1>
		<?php echo $username?> | Record
	</section>
	<section class="starting roster">
		STARTING <?php // echo $numstartingteams;?>
		<ul>
			<?php for ($i = 0; $i < $numstartingteams; $i++) {
				htmlOfSingleGame($games[$i], 'teamstart', $leagueid);
			} ?>
		</ul>
	</section>
	
	<section class="bench roster">
		BENCH
		<ul>
			<?php for ($i = 0; $i < $numbenchteams; $i++) {
				htmlOfSingleGame($benchgames[$i], 'teambench', $leagueid);
			} ?>
		</ul>
	</section>
	<?php if ($draftcomplete == false) {
		?><span>Draft is set for: <?php echo $drafttime;?></span><?php
	}?>
	<?php //check whether to display the Change Team Name button, which works off of $_SESSION['teamid']
	if (!isset($_SESSION['teamid'])){
		
	} else if ($username == $_SESSION['username'] && $_SESSION['teamid'] == $teamid){ ?>
	<nav>
		<ul class="nav3">
			<li><a href="change_teamname.php">Change Team Name</a></li>
		</ul>
	</nav>
	<?php } ?>
</body>
</html>

