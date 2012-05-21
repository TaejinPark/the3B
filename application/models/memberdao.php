<?php
include_once(APPPATH.'models/member.php');

class MemberDAO extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function getUser($user_id){
		$this->db->where('userID',$user_id)->from('member');

		if($this->db->count_all_results()==0) return new Member();

		$query = $this->db->where('userID',$user_id)->get('member');
		return $this->_makeMember($query->row());
	}

	function updateUser($user,$original){
		$data = $this->_preProcessUser($user);

		$data->setPassword(md5($data->getPassword()));

		if($data->getUserID()!=$original->getUserID()) return -1;
		$record = $this->db->where('nickname',$data->getNickname())->get('member')->row();
		if($record->userID!=$original->getUserID() && $record->nickname == $data->getNickname()) return -2;

		$this->db->where('userID',$data->getUserID())->update('member',$data);
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function updateSessionID($user_id,$sessionid){
		$this->db->set('sessionid',$sessionid)->where('userID',$user_id)->update('member');
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function deleteUser($user_id){
		$this->db->where('userID',$user_id)->delete('member');
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function getUserByNickname($nickname){
		$this->db->where('nickname',$nickname)->from('member');

		if($this->db->count_all_results()==0) return new Member();

		$query = $this->db->where('nickname',$nickname)->get('member');
		return $this->_makeMember($query->row());
	}

	function getUserBySessionID($sessionid){
		$this->db->where('sessionid',$sessionid)->from('member');

		if($this->db->count_all_results()==0) return new Member();

		$query = $this->db->where('sessionid',$sessionid)->get('member');
		return $this->_makeMember($query->row());
	}

	function insertUser($data){
		$data = $this->_preProcessUser($data);

		$existUserID = $this->db->where('userID',$data->getUserID())->from('member')->count_all_results();
		if($existUserID) return -1;
		$existNickname = $this->db->where('nickname',$data->getNickname())->from('member')->count_all_results();
		if($existNickname) return -2;

		$data->setPassword(md5($data->getPassword()));
		$this->db->set('userID',$data->getUserID());
		$this->db->set('nickname',$data->getNickname());
		$this->db->set('password',$data->getPassword());
		$this->db->insert('member');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function _makeMember($data){
		$member = new Member();
		$member->setUserID($data->userID);
		$member->setPassword($data->password);
		$member->setNickname($data->nickname);
		$member->setPenalty($data->penalty);
		$member->setSessionID($data->sessionid);
		return $member;
	}

	function _preProcessUser($user){
		$user->setUserID(trim($user->getUserID()));
		$user->setNickname(trim($user->getNickname()));
		return $user;
	}
}
?>