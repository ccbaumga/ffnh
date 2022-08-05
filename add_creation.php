<?php 
function add($nflteam, $instance) {
	$error = "";
	$pdo = db_connect();
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . "). <br>";
		}
	if ($error) {
		return $error;
	}
    if ($row['owner']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . ") is already owned. <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set owner = ? , status = "bench"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$_SESSION['teamid'], $nflteam, $instance, $_SESSION['leagueid']]);
	$chatsmessage = $_SESSION['teamname'] . " (" . $_SESSION['username'] . ") has added " . $instance . " " . $nflteam . ".";
	$statement = $pdo->prepare('insert into chats
	(user, message, leagueid) values
	(?, ?, ?)');
    $statement->execute(["TRANSACTION", $chatsmessage, $_SESSION['leagueid']]);
	redirect("team.php");
	
}
function adddrop($nflteam, $instance, $dropteam) {
	$error = "";
	$instancedrop = strtok($dropteam, " ");
	$nflteamdrop = strtok(" ");
	$pdo = db_connect();
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . "). Attempted to add from adddrop. <br>";
		}
	if ($error) {
		return $error;
	}
    if ($row['owner']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . ") is already owned. adddrop <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteamdrop, $instancedrop, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instancedrop . ") of this team (" . $nflteamdrop . ") in this league (" . $_SESSION['leagueid'] . "). Attempted to drop from adddrop. <br>";
		}
	if ($error) {
		return $error;
	}
	if ($row['owner'] <> $_SESSION['teamid']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is not owned by this team (" . $_SESSION['leagueid'] . "). Attempted to drop from adddrop. <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set owner = NULL, status = NULL
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteamdrop, $instancedrop, $_SESSION['leagueid']]);
	$statement = $pdo->prepare('update nflteaminstances 
	set owner = ? , status = "bench"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$_SESSION['teamid'], $nflteam, $instance, $_SESSION['leagueid']]);
	$chatsmessage = $_SESSION['teamname'] . " (" . $_SESSION['username'] . ") has added " . $instance . " " . $nflteam . " and dropped " . $instancedrop . " " . $nflteamdrop . ".";
	$statement = $pdo->prepare('insert into chats
	(user, message, leagueid) values
	(?, ?, ?)');
    $statement->execute(["TRANSACTION", $chatsmessage, $_SESSION['leagueid']]);
	redirect("team.php");
	
}

function checkRosterFull() {
	$error = "";
	$pdo = db_connect();
	$statement = $pdo->prepare('select leagueid, rosterlimit from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such league (" . $_SESSION['leagueid'] . "). <br>";
		}
	if ($error) {
		return $error;
	}
	if (is_null($row['rosterlimit'])) {
		return false;
	}
	if($row['rosterlimit'] < 0){
		$error = $error . "The league (" . $_SESSION['leagueid'] . ") has negative rosterlimit. <br>";
	}
	if ($error) {
		return $error;
	}
	$rostermax = $row['rosterlimit'];
	$statement = $pdo->prepare('select count(*) as curinst from nflteaminstances
	where league = ? and owner = ?');
	$statement->execute([$_SESSION['leagueid'], $_SESSION['teamid']]);
	$row = $statement->fetch();
	$rostercur = $row['curinst'];
	if($rostercur >= $rostermax){
		return $rostercur;
	} else {
		return false;
	}
}

function drop($nflteam, $instance) {
	$error = "";
	$pdo = db_connect();
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . "). <br>";
		}
	if ($error) {
		return $error;
	}
	if ($row['owner'] <> $_SESSION['teamid']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is not owned by this team (" . $_SESSION['leagueid'] . "). <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set owner = NULL, status = NULL
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	$chatsmessage = $_SESSION['teamname'] . " (" . $_SESSION['username'] . ") has dropped " . $instance . " " . $nflteam . ".";
	$statement = $pdo->prepare('insert into chats
	(user, message, leagueid) values
	(?, ?, ?)');
    $statement->execute(["TRANSACTION", $chatsmessage, $_SESSION['leagueid']]);
	redirect("team.php");
}
?>