<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="league_creation_settings.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php
	include("session_handling.php");
	include("db.php");
	$pdo = $db;
	ensure_logged_in();
	include("header.html");
	include("add_creation.php");
	$dropFailed = FALSE;
	if($_SERVER["REQUEST_METHOD"] == "GET") {
		$dropteam = $_GET["nflteam"];
		$dropinstance = $_GET["instance"];
		$error = drop($dropteam, $dropinstance);
		$addFailed = TRUE;
		echo $error;
		die();
	}?>
</body>