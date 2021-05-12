<?php 
function formGames($pdo, $teamid, $startstatus, &$games, $week){
	$statement = $pdo->prepare('SELECT i.nflteam, i.instancenumber, i.status, 
	n.location, n.wins, n.losses, n.ties
	from nflteaminstances i
	join nflteams n on n.abbr = i.nflteam
	where owner = ?
	and status = ?');
	$statement->execute([$teamid, $startstatus]);
	$sqlarray = [];
	$numteams = 0;
	while ($row = $statement->fetch()) {
		$numteams = array_push($sqlarray, $row);
	}
	for ($i = 0; $i < $numteams; $i++) {
		array_push($games, new gameinstance);
		gameCreate($games[$i], $sqlarray[$i], $pdo, $week);
	}
} ?>