<?php

//require_once('db_credentials.php');

function db_connect() {
	try {
		$pdo = new PDO("mysql:host=localhost;dbname=baumgc12", 
		"baumgc12", "mysql884812", array(PDO::ATTR_ERRMODE => 
		PDO::ERRMODE_EXCEPTION));
	} catch (PDOException $e) {
		echo "Cannot connect to the database";
		exit();
	}
	return $pdo;
}

function db_disconnect() {
	global $db;
	$db = null;
}

$db = db_connect();

function register($username, $password) {
	global $db;
	$registered = FALSE;
	try{
		$statement = $db->prepare("INSERT INTO profiles 
		(username, password) values (?, ?)");
		$statement->execute([$username, crypt($password)]);
		$registered = TRUE;
	} catch (PDOException $e) {
		//echo "ERROR";
		//var_dump($e);
	}
	return $registered;
}

function is_password_correct($username, $password) {
	global $db;
	$password_correct = FALSE;
	$statement = $db->prepare("SELECT password FROM profiles WHERE username = ?");
	$statement->execute([$username]);
	
	if ($statement) {
		//$row = $statement->fetch();
		foreach ($statement as $row) {
		$correct_password = $row["password"];
		/*if (hash_equals($hashed_password, crypt($password, $correct_password))) {
			$password_correct = TRUE;
		}*/
		$password_correct = $correct_password === crypt($password, $correct_password);
		}
	}
	return $password_correct;
}

function get_chats($last_id = -1, $leagueid = -1) {
  global $db;
  $result = [];
  try {
    $sql = "SELECT id, user, message FROM chats WHERE id > ?
			and leagueid = ? ORDER BY id";
    $statement = $db->prepare($sql);
    $statement->execute([$last_id, $leagueid]);
    $chats = $statement->fetchAll(PDO::FETCH_ASSOC); // puts in associative array (ready for json)
    
    $result["chats"] = $chats;
    $result["status"] = "ok";
  } 
  catch (PDOException $e) {
    $result["status"] = "error";
    $result["error"] = $e;
  }
  return $result;
}

/**
 * Inserts a chat for the given user and message
 * Returns associative array to indicate success or error
 */
function insert_chat($user, $message, $leagueid) {
  global $db;
  $result = [];
  try {
    $sql = "INSERT INTO chats(user, message, leagueid) VALUES(?, ?, ?)";
    $statement = $db->prepare($sql);
    $statement->execute([$user, $message, $leagueid]);
    $result["status"] = "ok";
  } 
  catch (PDOException $e) {
    $result["status"] = "error";
    $result["error"] = $e;
  }
  return $result;
}
?>