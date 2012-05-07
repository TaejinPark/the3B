<?php
class Room {
	private $room_seq;
	private $name;
	private $maxuser;
	private $private;
	private $password;
	private $gmaetype;
	private $gameoption;
	private $roomtype;
	private $owner;

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
		return $this->gmaetype;
	}

	function setGameType($gmaetype){
		$this->gmaetype = $gmaetype;
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
}
?>