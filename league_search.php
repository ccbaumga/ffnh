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
	<?php include("session_handling.php");
	ensure_logged_in();
	include("header.html"); 
	if (!isset($_GET['search'])){
		$search = "";
	} else {
		$search = $_GET['search'];
	}
	//unset all session variables except username
	$username = $_SESSION['username'];
	session_unset();
	$_SESSION['username'] = $username;
	?>
	<h2>League Search</h2>
	<form id="search" action="league_search.php" method="get">
		<table>
			<tr>
				<td>Enter League Name:</td>
				<td><input type="text" name="search" id="search" value="<?php echo $search;?>"></td>
				<td><input type="submit" value="Search"></td>
			</tr>
		</table>
	</form>
	<?php
	if ($search){
		include("db.php");
		$pdo = $db;
		$statement = $pdo->prepare('select leagueid, leaguename, admin from leagues
		where privacy = "public" and teamslocked = 0 and leaguename like ?');
		$statement->execute([$search . "%"]);
		?>
		<table>
			<tr>
				<th>League ID</th>
				<th>League Name</th>
				<th>Admin</th>
			</tr>
			<?php while ($row = $statement->fetch()){
				?><tr>
				<td><?php echo $row['leagueid'];?></td>
				<td><a href="standings.php?search=<?php echo $row['leagueid'];?>"><?php echo $row['leaguename'];?></td>
				<td><?php echo $row['admin'];?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } ?>