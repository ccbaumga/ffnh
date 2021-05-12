<?php 
function create_league($leaguename, $private, $teamname, $numinstances) {
	$error = "";
	
	/*check for simple errors*/
	$leaguename = trim($leaguename);
	if (!$leaguename) {
		$error .= "Please enter a league name. " . "<br>";
	}
	
	/*$numteams = intval($numteams);
	if ($numteams == 0 || !is_int($numteams)) {
		$error .= "Number of teams must be a positive integer. " . "<br>";
	} else if ($numteams % 2 != 0) {
		$error .= "Number of teams must be even. " . "<br>";
	}*/
	
	if (!$numinstances || $numinstances === "0") {
		$numinstances = 0;
	} else {
		$numinstances = intval($numinstances);
		if ($numinstances == 0 || !is_int($numinstances)) {
			$error .= "Number of instances must be an integer. " . "<br>";
		} else if ($numinstances < 0) {
		$error .= "Number of instances must be non-negative. " . "<br>";
		}
	}
	
	if ($private) {
		$privatetext = 'private';
	} else {
		$privatetext = 'public';
	}
	
	$teamname = trim($teamname);
	
	if ($error) {
		return $error;
	}
	
	/*sql insert into leagues*/
	$pdo = db_connect();
	$statement = $pdo->prepare('insert into leagues
	(leaguename, admin, privacy, maxinstances) values 
	(?, ?, ?, ?)');
	$statement->execute([$leaguename, $_SESSION['username'], $privatetext, $numinstances]);
	/*confirm league creation*/
	$statement = $pdo->prepare('select last_insert_id()');
	$statement->execute([]);
	$row = $statement->fetch();
	echo "$row[0] : " . $row[0];
	$extra = 0;
	while ($extrastatements = $statement->fetch()) {
		$extra++;
	}
	if ($extra != 0) {
		return "Extra last_insert_id statements";
	}
	if ($row[0] === 0) {
		return "Last_insert_id returns 0";
	}
	$leagueid = $row[0];
	echo "$leagueid : " . $leagueid;
	if ($leagueid == null) {
		return "leagueid is null";
	}
	$_SESSION['leagueid'] = $row[0];
	$_SESSION['leaguename'] = $leaguename;
	
	/*create all the instances*/
	for ($i = 1; $i <= $numinstances; $i++) {
		batchofnflteams($pdo, $leagueid, $i);
	}
	/*confirm instance creation*/
	$statement = $pdo->prepare('select nflteam
	from nflteaminstances
	where league = ?');
	$statement->execute([$leagueid]);
	$instcounter = 0;
	while ($row = $statement->fetch()) {
		$instcounter++;
	}
	if ($instcounter != $numinstances * 32) {
		return "Instance counter doesn't match";
	}
	
	/*put user in the league*/
	if ($teamname) {
		$statement = $pdo->prepare('insert into fantasyteams
		(owner, league, teamname) values 
		(?, ?, ?)');
		$statement->execute([$_SESSION['username'], $leagueid, $teamname]);
		/*check new team creation*/
		$statement = $pdo->prepare('select last_insert_id()');
		$statement->execute([]);
		$row = $statement->fetch();
		echo "$row[0] : " . $row[0];
		$extra = 0;
		while ($extrastatements = $statement->fetch()) {
			$extra++;
		}
		if ($extra != 0) {
			return "Extra last_insert_id statements";
		}
		if ($row[0] === 0) {
			return "Last_insert_id returns 0";
		}
		$teamid = $row[0];
		$_SESSION['teamid'] = $teamid;
		$_SESSION['teamname'] = $teamname;
		redirect("team.php");
	}
	
	redirect("myteams.php");
}

function batchofnflteams($pdo, $leagueid, $instancenumber) {
	addinstance($pdo, $leagueid, $instancenumber, 'ARI');
	addinstance($pdo, $leagueid, $instancenumber, 'ATL');
	addinstance($pdo, $leagueid, $instancenumber, 'BAL');
	addinstance($pdo, $leagueid, $instancenumber, 'BUF');
	addinstance($pdo, $leagueid, $instancenumber, 'CAR');
	addinstance($pdo, $leagueid, $instancenumber, 'CHI');
	addinstance($pdo, $leagueid, $instancenumber, 'CIN');
	addinstance($pdo, $leagueid, $instancenumber, 'CLE');
	addinstance($pdo, $leagueid, $instancenumber, 'DAL');
	addinstance($pdo, $leagueid, $instancenumber, 'DEN');
	addinstance($pdo, $leagueid, $instancenumber, 'DET');
	addinstance($pdo, $leagueid, $instancenumber, 'GB');
	addinstance($pdo, $leagueid, $instancenumber, 'HOU');
	addinstance($pdo, $leagueid, $instancenumber, 'IND');
	addinstance($pdo, $leagueid, $instancenumber, 'JAX');
	addinstance($pdo, $leagueid, $instancenumber, 'KC');
	addinstance($pdo, $leagueid, $instancenumber, 'LA');
	addinstance($pdo, $leagueid, $instancenumber, 'LAC');
	addinstance($pdo, $leagueid, $instancenumber, 'LV');
	addinstance($pdo, $leagueid, $instancenumber, 'MIA');
	addinstance($pdo, $leagueid, $instancenumber, 'MIN');
	addinstance($pdo, $leagueid, $instancenumber, 'NE');
	addinstance($pdo, $leagueid, $instancenumber, 'NO');
	addinstance($pdo, $leagueid, $instancenumber, 'NYG');
	addinstance($pdo, $leagueid, $instancenumber, 'NYJ');
	addinstance($pdo, $leagueid, $instancenumber, 'PHI');
	addinstance($pdo, $leagueid, $instancenumber, 'PIT');
	addinstance($pdo, $leagueid, $instancenumber, 'SEA');
	addinstance($pdo, $leagueid, $instancenumber, 'SF');
	addinstance($pdo, $leagueid, $instancenumber, 'TB');
	addinstance($pdo, $leagueid, $instancenumber, 'TEN');
	addinstance($pdo, $leagueid, $instancenumber, 'WAS');
	
}

function addinstance($pdo, $leagueid, $instancenumber, $nflteam) {
	$statement = $pdo->prepare('insert into nflteaminstances
	(nflteam, league, instancenumber) values 
	(?, ?, ?)');
	$statement->execute([$nflteam, $leagueid, $instancenumber]);
}

?>