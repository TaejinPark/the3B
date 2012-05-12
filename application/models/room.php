<?php
class Room {
	public $room_seq;
	public $name;
	public $maxuser;
	public $private;
	public $password;
	public $gametype;
	public $gameoption;
	public $roomtype;
	public $owner;
	public $currentuser;

	function getRoomSeq(){
		return $this->room_seq;
	}

	function setRoomSeq($room_seq){
		$this->room_seq = $room_seq;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}

	function getMaxUser(){
		return $this->maxuser;
	}

	function setMaxUser($maxuser){
		$this->maxuser = $maxuser;
	}

	function getPrivate(){
		return $this->private;
	}

	function setPrivate($private){
		$this->private = $private;
	}

	function getPassword(){
		return $this->password;
	}

	function setPassword($password){
		$this->password = $password;
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

	function getRoomType(){
		return $this->roomtype;
	}

	function setRoomType($roomtype){
		$this->roomtype = $roomtype;
	}

	function getOwner(){
		return $this->owner;
	}

	function setOwner($owner){
		$this->owner = $owner;
	}

	function getCurrentUser(){
		return $this->currentuser;
	}

	function setCurrentUser($currentuser){
		$this->currentuser = $currentuser;
	}
}
?>