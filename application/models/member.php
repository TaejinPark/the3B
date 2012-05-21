<?php
class Member {
	private $userID;
	private $password;
	private $nickname;
	private $penalty;
	private $sessionid;

	function getUserID(){
		return $this->userID;
	}

	function setUserID($userID){
		$this->userID = $userID;
	}

	function getPassword(){
		return $this->password;
	}

	function setPassword($password){
		$this->password = $password;
	}

	function getNickname(){
		return $this->nickname;
	}

	function setNickname($nickname){
		$this->nickname = $nickname;
	}

	function getPenalty(){
		return $this->penalty;
	}

	function setPenalty($penalty){
		$this->penalty = $penalty;
	}

	function getSessionID(){
		return $this->sessionid;
	}

	function setSessionID($sessionid){
		$this->sessionid = $sessionid;
	}
}
?>