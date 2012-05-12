<?php
class Result{
	private $seq;
	private $userID;
	private $gametype;
	private $gameoption;
	private $time;
	private $result;
	
	function getSeq(){
		return $this->seq;
	 }

	function setSeq($seq){
		$this->seq = seq;
	}

	function getUserID($userID){
	return $this->userID;
	}

	function setUserID($userID){
		$this->userID = $userID;
	}

	function getGameType(){
		return $this->gametype;
	}

	function setGameType($gametype){
		$this->gametype = $gametype;
	}

	function getGameOption(){
		return $this->gameoption;
	}

	function setGameOption($gameoption){
		$this->gameoption = $gameoption;
	}

	function getTime(){
		return $this->time;
	}

	function setTime($time){
		$this->time = $time;
	}

	function getResult(){
		return $this->result;
	}

	function setResult($result){
		$this->result = $result;
	}
}
?>
