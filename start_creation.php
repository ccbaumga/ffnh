<?php 
function start($nflteam, $instance) {
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
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . ") is not yours (" . $_SESSION['teamid'] . "). <br>";
	}
	if ($row['status'] == "starting"){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is already starting. <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set status = "starting"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	redirect("team.php");
	
}
function startbench($nflteam, $instance, $benchteam) {
	$error = "";
	$instancebench = strtok($benchteam, " ");
	$nflteambench = strtok(" ");
	$pdo = db_connect();
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . "). Attempted to start from startbench. <br>";
		}
	if ($error) {
		return $error;
	}
    if ($row['owner'] <> $_SESSION['teamid']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") in this league (" . $_SESSION['leagueid'] . ") is not yours (" . $_SESSION['teamid'] . "). startbench <br>";
	}
	if ($row['status'] == "starting"){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is already starting. startbench <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('select nflteam, league, instancenumber, owner, status from nflteaminstances
	where nflteam = ? and instancenumber = ? and league = ?');
	$statement->execute([$nflteambench, $instancebench, $_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such instance (" . $instancedrop . ") of this team (" . $nflteamdrop . ") in this league (" . $_SESSION['leagueid'] . "). Attempted to bench from startbench. <br>";
		}
	if ($error) {
		return $error;
	}
	if ($row['owner'] <> $_SESSION['teamid']){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is not owned by this team (" . $_SESSION['leagueid'] . "). Attempted to bench from startbench. <br>";
	}
	if ($row['status'] <> "starting"){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is not in the starting lineup. Attempted to bench from startbench. <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set status = "bench"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteambench, $instancebench, $_SESSION['leagueid']]);
	$statement = $pdo->prepare('update nflteaminstances 
	set status = "starting"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	redirect("team.php");
	
}

function checkStartingLineupFull() {
	$error = "";
	$pdo = db_connect();
	$statement = $pdo->prepare('select leagueid, maxstart from leagues
	where leagueid = ?');
	$statement->execute([$_SESSION['leagueid']]);
	$row = $statement->fetch();
	if ($row == false){
		    $error = $error . "No such league (" . $_SESSION['leagueid'] . "). <br>";
		}
	if ($error) {
		return $error;
	}
	if (is_null($row['maxstart'])) {
		return false;
	}
	if($row['maxstart'] < 0){
		$error = $error . "The league (" . $_SESSION['leagueid'] . ") has negative maxstart. <br>";
	}
	if ($error) {
		return $error;
	}
	$startmax = $row['maxstart'];
	$statement = $pdo->prepare('select count(*) as curstart from nflteaminstances
	where league = ? and owner = ? and status = "starting"');
	$statement->execute([$_SESSION['leagueid'], $_SESSION['teamid']]);
	$row = $statement->fetch();
	$startcur = $row['curstart'];
	if($startcur >= $startmax){
		return $startcur;
	} else {
		return false;
	}
}

function bench($nflteam, $instance) {
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
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is not owned by this team (" . $_SESSION['teamid'] . "). <br>";
	}
	if ($row['bench'] == "bench"){
		$error = $error . "This instance (" . $instance . ") of this team (" . $nflteam . ") is already on the bench. <br>";
	}
	if ($error) {
		return $error;
	}
	$statement = $pdo->prepare('update nflteaminstances 
	set status = "bench"
	where nflteam = ? and instancenumber = ? and league = ?');
    $statement->execute([$nflteam, $instance, $_SESSION['leagueid']]);
	redirect("team.php");
}
?>