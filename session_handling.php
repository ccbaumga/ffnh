<?php
session_start();

/*redirects user to login page if not logged in*/
function ensure_logged_in() {
	if (!isset($_SESSION["username"])) {
		redirect("home.php");
	}
}

/*redirects to a specific url*/
function redirect($url) {
	header("Location: $url");
	die;
}

?>