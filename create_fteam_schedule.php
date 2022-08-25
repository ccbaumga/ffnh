<?php
const max_iter = 2000;
const max_teams = 10;

function lock_teams($leagueid){
	$pdo = db_connect();
	
	$statement = $pdo->prepare('select currentweek from globals');
	$statement->execute([]);
	$row = $statement->fetch();
	$currentweek = $row['currentweek'];
	
	$statement = $pdo->prepare('select count(*) as numteams from fantasyteams
	where league = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	$numteams = $row['numteams'];
	
	$statement = $pdo->prepare('select leaguename, admin, teamslocked, regularweeks
	from leagues where leagueid = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	
	if ($row['admin'] <> $_SESSION['username']){
		return "You (" . $_SESSION['username'] . ") are not the admin (" . $row['admin'] . ") of this league (" . $leagueid . "," . $row['leaguename'] . ").";
	}
	if ($row['teamslocked'] <> 0){
		return "Teams in this league (" .  $leagueid . "," . $row['leaguename'] . ") are already locked.";
	}
	if ($numteams % 2 == 1){
		return "Number of teams in this league (" . $leagueid . "," . $row['leaguename'] . ") is odd (" . $numteams . 
		"). Current scheduling algorithm only supports an even number of teams.";
	}
	if ($numteams > max_teams){
		return "Number of teams in this league (" . $leagueid . "," . $row['leaguename'] . ") is above " . max_teams . " (" . $numteams . 
		"). Current scheduling algorithm only supports teams up to that.";
	}
	//echo $row['regularweeks'] . "<br>" . strpos($row['regularweeks'], ",") . "<br>" . substr($row['regularweeks'], strpos($row['regularweeks'], ",") + 1);
	$lastweek = substr($row['regularweeks'], strpos($row['regularweeks'], ",") + 1);
	$firstweek = substr($row['regularweeks'], 0, strpos($row['regularweeks'], ","));
	echo "<br>" . $lastweek . "<br>" . $firstweek . "<br>" . intval("a") . "<br>";
	if (intval($lastweek) > 0 && intval($firstweek) > 0){
		$lastweek = intval($lastweek);
		$firstweek = intval($firstweek);
	} else {
		return "regulerweeks in db are not numbers. error.";
	}
	if ($lastweek < $currentweek){
		return "Season is already over. Your last week or regular season (" . $lastweek . ") has already passed.";
	}
	if ($firstweek < $currentweek){
		$firstweek = $currentweek;
		$statement = $pdo->prepare('update leagues set regularweeks = ? where leagueid = ?');
		$statement->execute([$firstweek . "," . $lastweek, $leagueid]);
	}
	
	$schedulearray = create_schedule($numteams, $firstweek, $lastweek);
	
	$statement = $pdo->prepare('select teamid from fantasyteams
	where league = ?');
	$statement->execute([$leagueid]);
	$teamidarray = [-1]; // filler because there is no team 0 in schedulearray
	for ($x = 0; $x < $numteams; $x++){
		$row = $statement->fetch();
		array_push($teamidarray, $row['teamid']);
	}
	//insert all the games
	for ($x = 0; $x < count($schedulearray); $x++){
		for ($y = 0; $y < count($schedulearray[0]); $y+=2){
			$statement = $pdo->prepare('insert into fantasymatchups (week, hometeam, awayteam)
			values (?, ?, ?)');
			$statement->execute([$firstweek + $x, $teamidarray[$schedulearray[$x][$y]], $teamidarray[$schedulearray[$x][$y + 1]]]);
		}
	}
	//change the leagues teamslocked values
	$statement = $pdo->prepare('update leagues set teamslocked = 1 where leagueid = ?');
	$statement->execute([$leagueid]);
	$statement = $pdo->prepare('insert into chats (user, message, leagueid) VALUES (?, ?, ?)');
	$statement->execute(["ADMIN", "(" . $_SESSION['username'] . ") has locked teams and generated matchup schedule", $_SESSION['leagueid']]);
	return false;
	return "no code";
}

function create_schedule($nt, $firstweek, $lastweek){
	//$nt = 7; //testing purposes
	$numweeks = $lastweek - $firstweek + 1;
	if ($numweeks <= $nt - 1){
		$weeks = $numweeks;
	} else if ($nt % 2 == 0){
		$weeks = $nt - 1;
	} else {
		$weeks = $nt;
	}
	if ($nt > max_teams) {
		return schedule_over_max($nt, $weeks);
	}
	
	$c = max_iter;
	$t = 0;
	while ($c == max_iter && $t < max_iter){
		$sa = [];
		$c = try_create($sa, $nt, $weeks);
		$t++;
	}
	$fullreps = (int)($numweeks / count($sa));
	$fullarray = [];
	for ($x = 0; $x < $fullreps; $x++){
		for ($y = 0; $y < count($sa); $y++){
			array_push($fullarray, $sa[$y]);
		}
		$sa = flip_home_away($sa);
	}
	$extraweeks = $numweeks % (count($sa));
	for ($y = 0; $y < $extraweeks; $y++){
		array_push($fullarray, $sa[$y]);
	}

	printarray($sa);
	echo "<br><br>";
	printarray($fullarray);
	echo $c;
	echo "<br>";
	echo $t;
	echo "<br>";
	//printarray([[1, 2, 3, 4, 5], [2, 3, 4, 5, 1]]);
	echo ((int)(count($sa[0]) / 2)) * 2;
	
	//create homeaway counter
	$ha2 = ["void"];
	for ($x = 1; $x <= $nt; $x++) {
		array_push($ha2, 0);
	}
	for ($x = 0; $x <= $nt; $x++) {
		echo $ha2[$x];
	}
	return $fullarray;
}

function try_create(&$sa, $nt, $numweeks){
	$c = 0;
	//create homeaway counter
	$ha = ["void"];
	for ($x = 1; $x <= $nt; $x++) {
		array_push($ha, 0);
	}
	// create bye array
	$byes = [];
	for ($x = 0; $x < $numweeks && $c < max_iter; $x++) {
		if (false) { // $x == 8
			
		} else {
			$cw = [];
			if ($nt % 2 == 1){
				//select a bye team
				place_bye($byes, $nt);
			} else {
				array_push($byes, -1);
			}
			for ($y = 0; $y < ($nt - $nt % 2) && $c < max_iter;) {
				$cg = [rn($nt), rn($nt)];
				if (isallowed($sa, $cw, $cg, $ha, $byes)){
					array_push($cw, $cg[0], $cg[1]);
					$y = $y + 2;
				}
				$c++;
			}
			array_push($sa, $cw);
		}
		
	}
	return $c;
}

function isallowed($sa, $currentweek, $currentgame, &$homeaway, $byes){
	// if a team is playing itself
	if ($currentgame[0] == $currentgame[1]){
		return false;
	}
	//if a team is already playing this week
	if (!(array_search($currentgame[0], $currentweek)===false)){
		return false;
	} else if (!(array_search($currentgame[1], $currentweek)===false)){
		return false;
	}
	//if a team is on bye this week
	if ($byes[count($sa)] == $currentgame[0] || $byes[count($sa)] == $currentgame[1]){
		return false;
	}
	//if this game would make a team more than 1 uneven in home/away
	if ($homeaway[$currentgame[0]] >= 1){
		return false;
	}
	if ($homeaway[$currentgame[1]] <= -1){
		return false;
	}
	//check if current game already exists in $sa
	$numweeks = count($sa);
	if ($numweeks == 0){
		$numgamesperweek = 0;
	} else {
		$numgamesperweek = (int)(count($sa[0]) / 2);
	}
	for ($x = 0; $x < $numweeks; $x++){
		for ($y = 0; $y < $numgamesperweek * 2; $y+=2){
			if ($sa[$x][$y] == $currentgame[0] && $sa[$x][$y + 1] == $currentgame[1]){
				return false;
			} else if ($sa[$x][$y] == $currentgame[1] && $sa[$x][$y + 1] == $currentgame[0]){
				return false;
			}
		}
	}
	
	
	$homeaway[$currentgame[0]] = $homeaway[$currentgame[0]] + 1;
	$homeaway[$currentgame[1]] = $homeaway[$currentgame[1]] - 1;
	return true;
}

function place_bye(&$byes, $nt){
	$byeworks = false;
	while ($byeworks == false){
		$byetry = rn($nt);
		//search $byes
		if (array_search($byetry, $byes) === false){
			$byeworks = true;
		}
	}
	array_push($byes, $byetry);
}

function rn($numteams){
	$num = rand(1, $numteams);
	return $num;
}

function printarray($array){
	echo "[";
	for ($x = 0; $x < count($array); $x++){
		echo "[";
		for ($y = 0; $y < count($array[$x]); $y = $y + 2){
			echo "[H:" . $array[$x][$y] . ",A:" . $array[$x][$y + 1] . "]";
		}
		if (count($array[$x]) % 2 == 1){
			echo "[Bye:" . $array[$x][count($array[$x]) - 1] . "]";
		}
		echo "] <br>";
	}
	echo "] <br>";
}

function schedule_over_max($nt, $weeks){
	return [];
}

function flip_home_away($sa){
	for ($x = 0; $x < count($sa); $x++){
		for ($y = 0; $y < count($sa[0]); $y+=2){
			$away = $sa[$x][$y];
			$sa[$x][$y] = $sa[$x][$y + 1];
			$sa[$x][$y + 1] = $away;
		}
	}
	return $sa;
}

function unlock_teams($leagueid){
	$pdo = db_connect();
	
	$statement = $pdo->prepare('select currentweek from globals');
	$statement->execute([]);
	$row = $statement->fetch();
	$currentweek = $row['currentweek'];
	
	$statement = $pdo->prepare('select count(*) as numteams from fantasyteams
	where league = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	$numteams = $row['numteams'];
	
	$statement = $pdo->prepare('select leaguename, admin, teamslocked, drafttime
	from leagues where leagueid = ?');
	$statement->execute([$leagueid]);
	$row = $statement->fetch();
	
	if ($row['admin'] <> $_SESSION['username']){
		return "You (" . $_SESSION['username'] . ") are not the admin (" . $row['admin'] . ") of this league (" . $leagueid . "," . $row['leaguename'] . ").";
	}
	if ($row['teamslocked'] <> 1){
		return "Teams in this league (" .  $leagueid . "," . $row['leaguename'] . ") are already unlocked.";
	}
	
	$statement = $pdo->prepare('select teamid from fantasyteams
	where league = ?');
	$statement->execute([$leagueid]);
	$teamidarray = [-1]; // filler because there is no team 0 in schedulearray
	for ($x = 0; $x < $numteams; $x++){
		$row = $statement->fetch();
		array_push($teamidarray, $row['teamid']);
	}
	//insert all the games
	for ($x = 1; $x < count($teamidarray); $x++){
		$statement = $pdo->prepare('delete from fantasymatchups where hometeam = ? or awayteam = ?');
		$statement->execute([$teamidarray[$x], $teamidarray[$x]]);
	}
	//change the leagues teamslocked values
	$statement = $pdo->prepare('update leagues set teamslocked = 0 where leagueid = ?');
	$statement->execute([$leagueid]);
	$statement = $pdo->prepare('insert into chats (user, message, leagueid) VALUES (?, ?, ?)');
	$statement->execute(["ADMIN", "(" . $_SESSION['username'] . ") has unlocked teams", $_SESSION['leagueid']]);
	return false;
	return "no code";
}
?>