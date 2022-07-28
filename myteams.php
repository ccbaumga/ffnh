<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="myteams.css">
	<link rel="stylesheet" href="colors.css">
	<script src="myteams.js" defer></script>
</head>
<body>
	<!--body-->
	<?php include("session_handling.php");
	ensure_logged_in();?>
	<?php include("header.html");
	$username = $_SESSION["username"];
	
	//$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", "baumgc12", "mysql884812");
	include("db.php");
	$pdo = $db;
	
	/*get teamslist/leagueslist*/
	$statement = $pdo->prepare('SELECT l.leagueid, l.leaguename, t.teamid, t.teamname 
	from fantasyteams t 
	join leagues l on l.leagueid = t.league
	where owner = ?');
	$statement->execute([$username]);
	
	$array = [];
	$numteams = 0;
	while ($row = $statement->fetch()) {
		$numteams = array_push($array, $row);
		//echo "leagueid: $row[leagueid] league: $row[leaguename] teamid: $row[teamid] teamname: $row[teamname]<br>\n";
	}
	?>
	<nav>
		<ul class="button editprofile">
			<li><a href="profile_settings.html">Edit Profile: <?php echo $username?></a></li>
		</ul>
	</nav>
	<section class="myteamslist">
		<ul>
		<?php for ($i = 0; $i < $numteams; $i++){ ?>
			<li class="ateam">
				<!-- javascript will make this item clickable-->
				<h2>
					<img src="ffnh.png" alt="Team Image" style="float:left;width:42px;height:42px;">
					<?php echo $array[$i]['teamname'];?>
				</h2>
				<a href="team.php?teamid=<?php echo $array[$i]['teamid'];?>"></a>
				<?php echo $array[$i]['leaguename'] . " | Record";
		}?>
	</section>
	<nav>
		<ul class="button">
			<li><a href="league_creation_form.php">Create a League</a></li>
		</ul>
	</nav>
	<nav>
		<ul class="button">
			<li><a href="league_search.php">Join a League</a></li>
		</ul>
	</nav>
</body>
</html>

