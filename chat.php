<?php

require('db.php');
$db = db_connect();
header("Content-Type: application/json; charset=UTF-8");
/*
TODO:
Implement endpoints described in the lab writeup

Hints:
- Use $_SERVER["REQUEST_METHOD"] to detect if a request is a GET or POST request
- If it's a GET request, you can use empty($_GET) to check if there are no GET parameters
- Look at database.php to see what's implemented for you...
*/
if ($_SERVER["REQUEST_METHOD"] == 'GET') {
// GET Endpoints

	if (!isset($_GET['last_id'])) {
	// Endpoint for getting the complete chat history
  // GET /chat.php
		$result = get_chats(-1, $_GET['leagueid']);
		$myJSON = json_encode($result);
		echo $myJSON;
	} else {  
  // Endpoint for getting new chats since given last chat id
  // GET /chat.php?last_id=#
		$result = get_chats($_GET['last_id'], $_GET['leagueid']);
		$myJSON = json_encode($result);
		echo $myJSON;
	}
}
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
// Endpoint for user sending new chat
// POST /chat.php
	$_POST = json_decode(file_get_contents('php://input'), true);
	//$request = json_decode($_POST, false);
	$result = insert_chat($_POST['user'], $_POST['message'], $_POST['leagueid']);
	$myJSON = json_encode($result);
	echo $myJSON;

  // Hint: If using JSON data for POST, 
  // need to populate $_POST superglobal yourself with:
  // $_POST = json_decode(file_get_contents('php://input'), true);
  // Hint: Use JSON data, form data is restrictive


}
// Set content type to json, and output json in page to be sent



db_disconnect();

?>