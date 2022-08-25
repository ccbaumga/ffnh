<?php
include("session_handling.php");
include("db.php");

$registerFailed = FALSE;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = trim($_POST["username"]);
	$password = $_POST["password"];
	if (register($username, $password)) {
		$_SESSION["username"] = $username;
		redirect("myteams.php");
	} else {
		$registerFailed = TRUE;
	}
} 
include("globalconstants.php");?>
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
	<?php include("header.html"); ?>
	<h1>Fantasy Football No Huddle</h1>
	<p>Register below:</p>
	<section class="register">
		<h2>Register</h2>
	<?php if($registerFailed) { ?>
		<p>That username is already taken</p><?php
	} ?>
		<form id="register" class="login" action="register.php" method="post">
			<div>
				<label for="username">Username:</label>
				<input type="text" name="username" id="username" value="" maxlength="<?php echo $maxUsername;?>" >
			</div>
			<div>
				<label for="password">Password:</label>
				<input type="text" name="password" id="password" value="" >
			</div>
			<input type="submit" value="Register" >
		</form>
	</section>
</body>
</html>