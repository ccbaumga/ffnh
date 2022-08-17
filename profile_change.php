<?php
function change_profile($username){
	$error = [true, ""];
	$username = trim($username);
	if ($username == ""){
		return [true, "New Username cannot be blank/whitespace<br>"];
	}
	if ($username == $_SESSION['username']){
		return [false, ""];
	}
	$pdo = db_connect();
	$statement = $pdo->prepare('select username from profiles where username = ?');
	$statement->execute([$username]);
	$row = $statement->fetch();
	if ($row === false){
		$statement = $pdo->prepare('select username from profiles where username = ?');
		$statement->execute([$_SESSION['username']]);
		$row = $statement->fetch();
		if ($row === false){
			return [true, "Your current username (" . $_SESSION['username'] . ") does not exist. <br>"];
		}
		$statement = $pdo->prepare('update profiles set username = ? where username = ?');
		$statement->execute([$username, $_SESSION['username']]);
		$oldusername = $_SESSION['username'];
		$_SESSION['username'] = $username;
		return [false, "Successfully changed username from (" . $oldusername . ") to (" . $username . "). <br>"];
	} else {
		return [true, "Username (" . $row['username'] . ") already exists. <br>"];
	}
	
}

?>