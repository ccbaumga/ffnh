<?php
include("session_handling.php");
session_unset();
redirect("home.php");
?>