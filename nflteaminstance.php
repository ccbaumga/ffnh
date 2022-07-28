<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="gameinstance.css">
	<link rel="stylesheet" href="colors.css">
	<link rel="stylesheet" href="nflteaminstance.css">
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	include("nav.html");
	include("gameinstance.php");
	//$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", "baumgc12", "mysql884812");
	include ("db.php");
	$pdo = $db;
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	$currentweek = 1;
	$week = $currentweek;
	
	if (!isset($_GET['nflteam'])) {
		redirect("home.php");
	}
	$nflteam = $_GET['nflteam'];
	$instancenumber = $_GET['instance'];
	$statement = $pdo->prepare('SELECT owner, status
	from nflteaminstances
	where league = ?
	and nflteam = ?
	and instancenumber = ?');
	$statement->execute([$leagueid, $nflteam, $instancenumber]);
	$row = $statement->fetch();
	$instanceowner = $row[0];
	$status = $row[1];
	
	/* get game info*/
	$statement = $pdo->prepare('SELECT i.nflteam, i.instancenumber, i.status, 
	n.location, n.wins, n.losses, n.ties
	from nflteaminstances i
	join nflteams n on n.abbr = i.nflteam
	where league = ?
	and nflteam = ?
	and instancenumber = ?');
	$statement->execute([$leagueid, $nflteam, $instancenumber]);
	
	$row = $statement->fetch();
	$game = new gameinstance;
	gameCreate($game, $row, $pdo, $week);
	
	/*get schedule info*/
	$statement = $pdo->prepare('SELECT week, status, day, kickofftime, 
	quarter, clock, 
	hometeam homeabbr, t1.location hometeamName, t1.wins homewins, 
	t1.losses homelosses, t1.ties hometies, 
	awayteam awayabbr, t2.location awayteamName, t2.wins awaywins, 
	t2.losses awaylosses, t2.ties awayties,
	homescore, awayscore
	FROM nflgames m
	JOIN nflteams t1 ON t1.abbr = m.hometeam
	JOIN nflteams t2 ON t2.abbr = m.awayteam
	where m.hometeam = ?
	or m.awayteam = ?');
	$statement->execute([$nflteam, $nflteam]);
	
	$scheduledata = [];
	$numgames = 0;
	while ($row = $statement->fetch()) {
		$numgames = array_push($scheduledata, $row);
	}
	
	/*set weeks with game*/
	$maxweeks = 17;
	$weekswithgame = [];
	for ($i = 1; $i <= $maxweeks; $i++) {
		$weekswithgame[$i] = false;
	}
	for ($i = 0; $i < sizeof($scheduledata); $i++) {
		$weekswithgame[$scheduledata[$i]['week']] = $scheduledata[$i];
	}
	$games = [];
	for ($i = 1; $i <= $maxweeks; $i++) {
		if ($weekswithgame[$i] != false) {
			$games[$i] = new gameinstance;
			$games[$i]->week = $i;
			$games[$i]->myabbr = $nflteam;
			$games[$i]->status = $weekswithgame[$i]['status'];
			$games[$i]->day = $weekswithgame[$i]['day'];
			$games[$i]->kickofftime = $weekswithgame[$i]['kickofftime'];
			$games[$i]->quarter = $weekswithgame[$i]['quarter'];
			$games[$i]->clock = $weekswithgame[$i]['clock'];
			$games[$i]->ishome = $weekswithgame[$i]['homeabbr'] == $nflteam;
			if ($games[$i]->ishome){
				$games[$i]->mylocation = $weekswithgame[$i]['hometeamName'];
				$games[$i]->mywins = $weekswithgame[$i]['homewins'];
				$games[$i]->mylosses = $weekswithgame[$i]['homelosses'];
				$games[$i]->myties = $weekswithgame[$i]['hometies'];
				$games[$i]->myscore = $weekswithgame[$i]['homescore'];
				$games[$i]->oppabbr = $weekswithgame[$i]['awayabbr'];
				$games[$i]->opplocation = $weekswithgame[$i]['awayteamName'];
				$games[$i]->oppwins = $weekswithgame[$i]['awaywins'];
				$games[$i]->opplosses = $weekswithgame[$i]['awaylosses'];
				$games[$i]->oppties = $weekswithgame[$i]['awayties'];
				$games[$i]->oppscore = $weekswithgame[$i]['awayscore'];
			} else {
				$games[$i]->mylocation = $weekswithgame[$i]['awayteamName'];
				$games[$i]->mywins = $weekswithgame[$i]['awaywins'];
				$games[$i]->mylosses = $weekswithgame[$i]['awaylosses'];
				$games[$i]->myties = $weekswithgame[$i]['awayties'];
				$games[$i]->myscore = $weekswithgame[$i]['awayscore'];
				$games[$i]->oppabbr = $weekswithgame[$i]['homeabbr'];
				$games[$i]->opplocation = $weekswithgame[$i]['hometeamName'];
				$games[$i]->oppwins = $weekswithgame[$i]['homewins'];
				$games[$i]->opplosses = $weekswithgame[$i]['homelosses'];
				$games[$i]->oppties = $weekswithgame[$i]['hometies'];
				$games[$i]->oppscore = $weekswithgame[$i]['homescore'];
			}
		}
	}
	
	
	
	
	
	
	
	
	

	
	
	?>
	
	<section id="nflteaminstance">
		<section class="starting roster">
			<ul>
			<?php htmlOfSingleGame($game, 'teamstart', $leagueid);?>
			</ul>
		</section>
		<section class="actionbuttons">
			<ul>
			<?php if ($instanceowner == $teamid) {
				//if ($game->status == "upcoming") {
					if ($status == "starting") {
						?><li id="benchbutton"><a href="bench_confirm.php">Bench</a></li><?php
					} else if ($status == "bench") {
						?><li id="startbutton"><a href="start_confirm.php">Start</a></li><?php
					}
					?><li id="dropbutton"><a href="drop_confirm.php">Drop</a></li><?php
				//}
			} else if ($instanceowner == "") {
				?><li id="addbutton"><a href="add_confirm.php?leagueid=<?php echo $leagueid;
				?>&nflteam=<?php echo $game->myabbr?>&instance=<?php echo 
				$game->myinstancenumber?>">Add</a></li><?php
			} else {
				?><li id="tradebutton"><a href="trade_confirm.php">Trade</a></li><?php
			}?>
			</ul>
		</section>
		<section id="schedule">
			<table>
				<tr>
					<th>Wk</th>
					<th>Opponent</th>
					<th>Result</th>
				</tr>
				<?php for ($i = 1; $i < $maxweeks; $i++) {
					?><tr>
					<?php if ($weekswithgame[$i] != false) { ?>
						<td><?php echo $i;?></td>
						<td><?php echo $games[$i]->oppInfo();?></td>
						<td><?php echo $games[$i]->timeAndScore();?></td>
					<?php } else { ?>
						<td>Bye</td>
					<?php } ?>
					</tr><?php
				} ?>
			</table>
		</section>
	</section>