<?php
class ResultDAO extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function joinRoom($room_seq, $userID, $anonymous){
		$data = new RoomUser();
		$data->setRoomSeq($room_seq);
		$data->setUserID($userID);
		$data->setAnonymous($anonymous);

		$this->db->set($data)->insert('room_user');
		
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function leaveRoom($room_seq, $userID, $anonymous){
		$this->db->where('room_seq',$room_seq)->where('userID',$userID)->where('anonymous',$anonymous)->delete('room_user');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function getUserList($room_seq){
		$query = $this->db->where('room_seq',$room_seq)->get('room_user');

		$data = array();
		foreach($query->result() as $result){
			$tmp = new RoomUser();
			$tmp->setRoomSeq($result->room_seq);
			$tmp->setUserID($result->userID);
			$tmp->setAnonymous($result->Anonymous);
			$tmp->setReady($result->ready);
			$data[] = $tmp;
		}
		return $data;
	}

	function kickUser($room_seq, $userID, $anonymous){
		return $this->leaveRoom($room_seq, $userID, $anonymous);
	}
}
?>