<?php 
	//site: http://webdev.cs.uwosh.edu/students/baumgc12/project/home.php
	include("session_handling.php");
	include("db.php");
	
	$loginFailed = FALSE;
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		$username = $_POST["username"];
		$password = $_POST["password"];
		
		if (is_password_correct($username, $password)) {
			
			$_SESSION["username"] = $username;
			redirect("myteams.php");
		} else {
			$loginFailed = TRUE;
		}
	} else {
		$username = "";
		$password = "";
	}
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("header.html"); //echo $_SESSION["username"];?>
	<h1>Fantasy Football No Huddle</h1>
	<p>Tired of losing to nerds in fantasy football but don't want to spend 
	hours researching? Fantasy Football No Huddle is here!</p>
	<p>Instead of players, draft entire NFL teams to join your fantasy squad, and score points if 
	they win! </p>
	<p>No more worrying about injuries. No more deciphering coachspeak
	to determine playing time. No more garbage time production. Only wins and
	losses!</p>
	<p>Draft a team full of NFL teams. Get points if they win their actual NFL game. Some wins are worth more than others.</p>
	<p>Manage your team of teams throughout the season. Add unowned teams, make trades with other players, decide which teams to put in your starting lineup each week.</p>
	<p>Each week, compete head to head against another player. Whoever's NFL teams get more points from winning their game will be the fantasy winner! </p>
	<p>Log in or sign up below:</p>
	<section class="login">
		<h2>Log In</h2>
	<?php if($loginFailed) { ?>
		<p>Incorrect username or password</p><?php //echo crypt($password);
	} ?>
		<form id="login" class="login" action="home.php" method="post">
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="<?php echo $username ?>" >
			</div>
			<div>
				<label for="password">Password:</label>
				<input type="text" name="password" id="password" value="<?php echo $password ?>" >
			</div>
			<input type="submit" value="Log In" >
		</form>
	</section>
	<form id="register" action="register.php" method="get">
		<input type="submit" value="Register" >
	</form>
</body>
</html>