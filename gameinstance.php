<?php 
class gameinstance {

  // Property
	public $status;//
	public $day;//
	public $kickofftime;//
	public $quarter;//
	public $clock;//
	public $week;
	
  public $myabbr;//
	public $myimage;
	public $mylocation;//
	public $myinstancenumber;//
	public $mywins;//
	public $mylosses;//
	public $myties;//
	public $ishome;//
	public $isaway;
	public $isonbye;
	public $myscore;//
	
	public $oppabbr;//
	public $opplocation;//
	public $oppwins;//
	public $opplosses;//
	public $oppties;//
	public $oppscore;//
	
	
	
	

  // Method 
  function stringMyRecord() {
		if ($this->myties == 0){
			return $this->mywins . "-" . $this->mylosses;
		}
    return $this->mywins . "-" . $this->mylosses . "-" . $this->myties;
  }
	
	function stringOppRecord() {
		if ($this->oppties == 0){
			return $this->oppwins . "-" . $this->opplosses;
		}
    return $this->oppwins . "-" . $this->opplosses . "-" . $this->oppties;
  }
	
	function timeAndScore() {
		if ($this->status == 'upcoming'){
			return $this->day . " " . substr($this->kickofftime, 0, 5);
		} else if ($this->status == 'ongoing'){
			return "Q" . $this->quarter . " " . substr($this->clock, 3, 5) . " " . $this->myscore . "-" . $this->oppscore;
		} else if ($this->status == 'final'){
			$str = "";
			if ($this->myscore > $this->oppscore){
				$str = "W ";
			} else if ($this->myscore < $this->oppscore){
				$str = "L ";
			}
			return $str . $this->myscore . "-" . $this->oppscore;
		}
	}
	
	function oppInfo() {
		$str = "";
		if ($this->ishome){
			$str = "vs ";
		} else {
			$str = "@";
		}		
		return $str . $this->opplocation . " " . $this->stringOppRecord();
	}
	
	function oppShort() {
		$str = "";
		if ($this->ishome){
			$str = "vs ";
		} else {
			$str = "@";
		}		
		return $str . $this->oppabbr . " " . $this->stringOppRecord();
	}
	
	function getClass() {
		if ($this->isonbye) {
			$class = 'bye';
		}
		if ($this->status == 'upcoming') { 
			$class = 'yettoplay';
		}
		if ($this->status == 'ongoing') { 
			$class = 'inprogress';
		}
		if ($this->status == 'final' && $this->myscore > $this->oppscore) {
			$class = 'won';
		}
		if ($this->status == 'final' && $this->myscore < $this->oppscore) {
			$class = 'lost';
		}
		if ($this->status == 'final' && $this->myscore == $this->oppscore) {
			$class = 'tied';
		}
		return $class;
	}
} 

function gameCreate($game, $sqlarray, $pdo, $week) {
		$game->week = $week;
		$game->myabbr = $sqlarray['nflteam'];
		$game->mylocation = $sqlarray['location'];
		$game->myinstancenumber = $sqlarray['instancenumber'];
		$game->mywins = $sqlarray['wins'];
		$game->mylosses = $sqlarray['losses'];
		$game->myties = $sqlarray['ties'];
		$currstatement = $pdo->prepare('SELECT hometeam, awayteam, status, day, 
				kickofftime, homescore, awayscore, quarter, clock 
				from nflgames
				where week = ?
				and hometeam = ?
				or awayteam = ?
				and week = ?');
		//echo $sqlarray['nflteam'];
		$currstatement->execute([$week, $game->myabbr, $game->myabbr, $week]);
		$currgame = $currstatement->fetch();
		//echo "home: ";
		//echo $currgame['hometeam'];
		$game->ishome = $currgame['hometeam'] == $game->myabbr;
		$game->isaway = $currgame['awayteam'] == $game->myabbr;
		$game->isonbye = !$game->ishome && !$game->isaway;
		//echo $ishome;
		if (!$game->isonbye) {
			if ($game->ishome){
				$game->oppabbr = $currgame['awayteam'];
				$game->myscore = $currgame['homescore'];
				$game->oppscore = $currgame['awayscore'];
			} else {
				$game->oppabbr = $currgame['hometeam'];
				$game->myscore = $currgame['awayscore'];
				$game->oppscore = $currgame['homescore'];
			}
			$game->status = $currgame['status'];
			$game->day = $currgame['day'];
			$game->kickofftime = $currgame['kickofftime'];
			$game->quarter = $currgame['quarter'];
			$game->clock = $currgame['clock'];
			
			$currstatement = $pdo->prepare('select wins, losses, ties, location
			from nflteams
			where abbr = ?');
			$currstatement->execute([$game->oppabbr]);
			$oppinfo = $currstatement->fetch();
			$game->opplocation = $oppinfo['location'];
			$game->oppwins = $oppinfo['wins'];
			$game->opplosses = $oppinfo['losses'];
			$game->oppties = $oppinfo['ties'];
		}
}

function htmlOfSingleGame($game, $where, $leagueid) {
				$class = $game->getClass(); ?>
			<li class="<?php echo $class ?> gameinstance">
				<a href="nflteaminstance.php?leagueid=<?php echo $leagueid;
				?>&nflteam=<?php echo $game->myabbr?>&instance=<?php echo 
				$game->myinstancenumber?>"></a>
				
			<?php if ($game->isonbye) { ?>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname"> <?php echo $game->mylocation ?> </p>
					<p class="nflrecord"><?php echo $game->stringMyRecord() ?></p>
				</div>
				<div class="nflgameinfo">
					<p>Bye</p>
				</div>
			 
			<?php } else if ($where == 'matchup'){ ?>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname"> <?php echo $game->mylocation ?> </p>
					<p class="nflrecord"><?php echo $game->stringMyRecord() ?></p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore"><?php echo $game->timeAndScore() ?></p>
					<p class="opponent"><?php echo $game->oppShort() ?> </p>
				</div>
			<?php } else { ?>
				<div class="teamimage">
					<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
				</div>
				<div class="nflteaminfo">
					<p class="nflteamname"> <?php echo $game->mylocation ?> </p>
					<p class="nflrecord"><?php echo $game->stringMyRecord() ?></p>
				</div>
				<div class="nflgameinfo">
					<p class="timeandscore"><?php echo $game->timeAndScore() ?></p>
					<p class="opponent"><?php echo $game->oppInfo() ?> </p>
				</div> 
			<?php } ?>
			</li> <?php
}

?> 
<script src="gameinstance.js" defer></script>
