<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Fantasy Football No Huddle</title>
	<link rel="stylesheet" href="header.css">
	<link rel="stylesheet" href="team.css">
	<link rel="stylesheet" href="colors.css">
</head>
<body>
	<!--body-->
	<?php include("header.html"); 
	include("nav.html");?>
	<div id="week-selector">
		<select name="team1">
			<option value="1">Week 1</option>
			<option value="2">Week 2</option>
		</select>
	</div>
	<section class="teamname">
		<h1>
			<img src="ffnh.png" alt="Team Image" style="float:left;width:42px;height:42px;">
			Team Name
		</h1>
		Owner's Name | Record
	</section>
	<section class="starting roster">
		STARTING
		<ul>
			<li class="won">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Green Bay</p>
					<p class="nflrecord">6-2</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Final W 34-17</p>
					<p class="opponent">@San Francisco 4-5</p>
				</div>
			</li>
			<li class="yettoplay">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Minnesota</p>
					<p class="nflrecord">2-5</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Sun 12:00</p>
					<p class="opponent">@Detroit 3-4</p>
				</div>
			</li>
			<li class="inprogress">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Green Bay</p>
					<p class="nflrecord">6-2</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Q4 3:47 34-17</p>
					<p class="opponent">@San Francisco 4-5</p>
				</div>
			</li>
			<li class="lost">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">San Francisco</p>
					<p class="nflrecord">4-5</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Final L 17-34</p>
					<p class="opponent">@Green Bay 6-2</p>
				</div>
			</li>
		</ul>
	</section>
	
	<section class="bench roster">
		BENCH
		<ul>
			<li class="won">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Green Bay</p>
					<p class="nflrecord">6-2</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Final W 34-17</p>
					<p class="opponent">@San Francisco 4-5</p>
				</div>
			</li>
			<li class="yettoplay">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Minnesota</p>
					<p class="nflrecord">2-5</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Sun 12:00</p>
					<p class="opponent">@Detroit 3-4</p>
				</div>
			</li>
			<li class="inprogress">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">Green Bay</p>
					<p class="nflrecord">6-2</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Q4 3:47 34-17</p>
					<p class="opponent">@San Francisco 4-5</p>
				</div>
			</li>
			<li class="lost">
				<div class="position">
					<select name="team1">
						<option value="bench">Bench</option>
						<option value="starting">Starting</option>
					</select>
				</div>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname">San Francisco</p>
					<p class="nflrecord">4-5</p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore">Final L 17-34</p>
					<p class="opponent">@Green Bay 6-2</p>
				</div>
			</li>
		</ul>
	</section>
</body>
</html>

