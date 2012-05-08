<?php
class RoomUser {
	private $room_seq;
	private $userID;
	private $ready;
	private $anonymous;

	function getRoomSeq(){
		return $this->room_seq;
	}

	function setRoomSeq($room_seq){
		$this->room_seq = $room_seq;
	}

	function getUserID(){
		return $this->userID;
	}

	function setUserID($userID){
		$this->userID = $userID;
	}

	function getReady(){
		return $this->ready;
	}

	function setReady($ready){
		$this->ready = $ready;
	}

	function getAnonymous(){
		return $this->anonymous;
	}

	function setAnonymous($anonymous){
		$this->anonymous = $anonymous;
	}
}
?>
