<?php
include_once(APPPATH.'models/roomuser.php');

class RoomUserDAO extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function joinRoom($room_seq, $userID, $anonymous){
		$data = new RoomUser();
		$data->setRoomSeq($room_seq);
		$data->setUserID($userID);
		$data->setAnonymous($anonymous);
		$data->setReady(0);

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
			$tmp->setAnonymous($result->anonymous);
			$tmp->setReady($result->ready);
			$data[] = $tmp;
		}
		return $data;
	}

	function updateReady($room_seq,$userID,$ready){
		$this->db->set('ready',$ready)->where('room_seq',$room_seq)->where('userID',$userID)->update('room_user');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function kickUser($room_seq, $userID, $anonymous){
		return $this->leaveRoom($room_seq, $userID, $anonymous);
	}

	function existsUser($userID){
		$result = $this->db->where('userID',$userID)->from('room_user')->count_all_results();
		
		if($result>0) return true;
		return false;
	}
}
?>