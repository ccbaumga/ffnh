<?php 
include("globalconstants.php");

class settingsball{
	public $leaguename;
	public $private;
	public $numinstances;
	public $rostersize;
	public $startingsize;
	public $playoffteams;
	public $playoffweeks;
	public $standingstiebreaker;
	public $weeklytiebreaker;
}

function editsettings($newsettings, $pdo){
	$editFailed = [false, ""];
	
	$statement = $pdo->prepare('select count(*) as numteams from fantasyteams
	where league = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	$numteams = $row['numteams'];
	$statement = $pdo->prepare('select currentweek from globals');
	$statement->execute([]);
	$row = $statement->fetch();
	$currentweek = $row['currentweek'];
	$statement = $pdo->prepare('select count(*) as maxinstances from nflteaminstances
	where league = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	$maxinstances = $row['maxinstances'];
	$remainder = $maxinstances % 32;
	if ($remainder == 0){
		$maxinstances = $maxinstances / 32;
	} else {
		$maxinstances = "Not Divisible";
	}
	$statement = $pdo->prepare('select leaguename, admin, teamslocked, privacy, rosterlimit, maxstart, 
	drafttime, regularweeks, playoffweeks, playoffteams, standingstiebreaker, weeklytiebreaker, tiesetting from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		return [true, "No league id of " . $_SESSION['leagueid'] . " could be found."];
	}
	if ($_SESSION['username'] <> $row['admin']){
		return [true, "You (" . $_SESSION['username'] . ") are not the admin (" . $row['admin'] . ") of this league (" . $_SESSION['leagueid'] . ")."];
	}
	if ($newsettings->leaguename <> $row['leaguename'] && trim($newsettings->leaguename) <> $row['leaguename']){
		$statement = $pdo->prepare('update leagues set leaguename = ? where leagueid = ?');
		$statement->execute([trim($newsettings->leaguename), $_SESSION['leagueid']]);
		$editFailed = [false, "Successfully changed league name to: " . trim($newsettings->leaguename) . "<br>"];
	}
	if ($newsettings->private) {
		$privatetext = 'private';
	} else {
		$privatetext = 'public';
	}
	if ($privatetext <> $row['privacy']){
		$statement = $pdo->prepare('update leagues set privacy = ? where leagueid = ?');
		$statement->execute([$privatetext, $_SESSION['leagueid']]);
		$editFailed[1] = $editFailed[1] . "Successfully changed privacy to: " . $privatetext . "<br>";
	}
	$newsettings->numinstances = trim($newsettings->numinstances);
	if ($newsettings->numinstances <> $maxinstances){
		if ($row['drafttime'] < date("Y-m-d H:i:s") && !is_null($row['drafttime'])){
			$editFailed[1] = $editFailed[1] . "Draft has already passed. Cannot change number of instances.<br>";
			$editFailed[0] = true;
		} else {
			//delete existing instances
			$statement = $pdo->prepare('delete from nflteaminstances where league = ?');
			$statement->execute([$_SESSION['leagueid']]);
			/*create all the instances*/
			for ($i = 1; $i <= $newsettings->numinstances; $i++) {
				batchofnflteams($pdo, $_SESSION['leagueid'], $i);
			}
			/*confirm instance creation*/
			$statement = $pdo->prepare('select nflteam
			from nflteaminstances
			where league = ?');
			$statement->execute([$_SESSION['leagueid']]);
			$instcounter = 0;
			while ($subrow = $statement->fetch()) {
				$instcounter++;
			}
			if ($instcounter != $newsettings->numinstances * 32) {
				return "Instance counter doesn't match";
			}
			
			$editFailed[1] = $editFailed[1] . "Successfully changed number of instances to " . $newsettings->numinstances . ".<br>";
		}
	}
	$newsettings->rostersize = trim($newsettings->rostersize);
	$newsettings->startingsize = trim($newsettings->startingsize);
	if ($newsettings->rostersize <> $row['rosterlimit'] || $newsettings->startingsize <> $row['maxstart']){
		if ($newsettings->rostersize == "" || $newsettings->startingsize == ""){
			if ($newsettings->rostersize == "" && $newsettings->startingsize == ""){
				$statement = $pdo->prepare('update leagues set rosterlimit = NULL, maxstart = NULL where leagueid = ?');
				$statement->execute([$_SESSION['leagueid']]);
				$editFailed[1] = $editFailed[1] . "Successfully changed rosterlimit, maxstart to: NULL, NULL <br>";
			} else if ($newsettings->rostersize == ""){
				$statement = $pdo->prepare('update leagues set rosterlimit = NULL, maxstart = ? where leagueid = ?');
				$statement->execute([$newsettings->startingsize, $_SESSION['leagueid']]);
				$editFailed[1] = $editFailed[1] . "Successfully changed rosterlimit, maxstart to: NULL, " . $newsettings->startingsize . " <br>";
			} else {
				$statement = $pdo->prepare('update leagues set rosterlimit = ?, maxstart = ? where leagueid = ?');
				$statement->execute([$newsettings->rostersize, $newsettings->rostersize, $_SESSION['leagueid']]);
				$editFailed[1] = $editFailed[1] . "Successfully changed rosterlimit, maxstart to: " . $newsettings->rostersize . ", " . $newsettings->rostersize . " <br>";
			}
		} else if ($newsettings->startingsize > $newsettings->rostersize){
			$editFailed[1] = $editFailed[1] . "Starting Size cannot be greater than Roster Limit.<br>";
			$editFailed[0] = true;
		} else {
			$statement = $pdo->prepare('update leagues set rosterlimit = ?, maxstart = ? where leagueid = ?');
			$statement->execute([$newsettings->rostersize, $newsettings->startingsize, $_SESSION['leagueid']]);
			$editFailed[1] = $editFailed[1] . "Successfully changed rosterlimit, maxstart to: " . $newsettings->rostersize . ", " . $newsettings->startingsize . "<br>";
		}
	}
	if ($newsettings->playoffteams <> $row['playoffteams'] || $newsettings->playoffweeks <> $row['playoffweeks']){
		$editFailed[1] = $editFailed[1] . "Playoffs not currently set up.<br>";
		$editFailed[0] = true;
	}
	if ($newsettings->standingstiebreaker <> $row['standingstiebreaker'] || $newsettings->weeklytiebreaker <> $row['weeklytiebreaker']){
		$statement = $pdo->prepare('update leagues set standingstiebreaker = ?, weeklytiebreaker = ? where leagueid = ?');
		$statement->execute([$newsettings->standingstiebreaker, $newsettings->weeklytiebreaker, $_SESSION['leagueid']]);
		$editFailed[1] = $editFailed[1] . "Successfully changed tiebreakers to: " . $newsettings->standingstiebreaker . ", " . $newsettings->weeklytiebreaker . "<br>";
	}
	
	return $editFailed;
}
function create_league($leaguename, $private, $teamname) {
	$error = "";
	global $standardNumInstances;
	$numinstances = $standardNumInstances;
	
	/*check for simple errors*/
	$leaguename = trim($leaguename);
	if (!$leaguename) {
		$error .= "Please enter a league name. " . "<br>";
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
	(leaguename, admin, privacy) values 
	(?, ?, ?)');
	$statement->execute([$leaguename, $_SESSION['username'], $privatetext]);
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