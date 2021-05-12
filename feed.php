<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="colors.css">
  <link rel="stylesheet" href="feed.css">
	<?php include("session_handling.php");
	ensure_logged_in();
	
	/*i'm setting the cookies stuff*/
	$username = $_SESSION['username'];
	$leagueid = $_SESSION['leagueid'];
	$leaguename = $_SESSION['leaguename'];
	$teamid = $_SESSION['teamid'];
	$teamname = $_SESSION['teamname'];
	$currentweek = 1;
	$week = $currentweek;
	?>
	<script>const curUser = <?php echo '"' . $username . '"'?>;
	const leagueid = <?php echo $leagueid; ?></script>
  <script src="feed.js" defer></script>
</head>
<body>
<?php
	include("header.html"); 
	include("nav.html");?>
  
  <div id="root">
    <!-- Chat Window -->
    <div id="chat-window">
      <!-- Fake starter messages to show what should look like -->
      <!-- You may delete if you choose once you get everything working 
      <span class="user">User1:</span>
      <span class="message">Hey how's it going?</span>
    
      <span class="user">User2:</span>
      <span class="message">Good how about you?</span>
    
      <span class="user">User1:</span>
      <span class="message">Pretty good</span>
    
      <span class="user">User3:</span>
      <span class="message">Hey I'm good too, thanks for asking</span>-->

      <!-- More chats will go here! -->
    </div>

    <!-- Chat Inputs (text box and Send button) -->
    <form id="chat-form">
      <textarea id="input" rows="3" autofocus></textarea>
      <input type="submit" value="Send" id="send-btn">
    </form>

    <!-- Gif Button and div 
    <button id="gif-btn">Get Gifs</button>
    <div id="gifs">
      <!-- Populate using GIPHY API -->
    </div>
  </div>
</body>
</html>