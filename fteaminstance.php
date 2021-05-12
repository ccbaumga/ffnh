<?php
class fteaminstance {
	public $teamid;
	public $teamname;
	public $teamimage;
	public $wins;
	public $losses;
	public $ties;
	public $username;
	
	function stringMyRecord() {
		if ($this->ties == 0){
			return $this->wins . "-" . $this->losses;
		}
    return $this->wins . "-" . $this->losses . "-" . $this->ties;
  }
}

function fteamCreate($fteam, $sqlarray){
	$fteam->teamid = $sqlarray['teamid'];
	$fteam->teamname = $sqlarray['teamname'];
	$fteam->username = $sqlarray['owner'];
	$fteam->wins = $sqlarray['wins'];
	$fteam->losses = $sqlarray['losses'];
	$fteam->ties = $sqlarray['ties'];
}

function htmlSingleTeam($fteam) { ?>
	<li class="fteam">
		<?php //if ($fteam->teamimage){ ?>
		<a href="team.php?otherteamid=<?php echo $fteam->teamid;?>"></a>
		<div class="teamimage">
			<img src="ffnh.png" alt="Team Image" style="width:42px;height:42px;">
		</div>
		<?php //} ?>
		<div class="fteamname">
			<p> <?php echo $fteam->teamname . "&nbsp" ?> </p>
		</div>
		<div class="username">
			<p><?php echo $fteam->username ?></p>
		</div> 
		<div class="record">
			<p><?php echo $fteam->stringMyRecord() ?></p>
		</div>
	</li>
<?php }
?>