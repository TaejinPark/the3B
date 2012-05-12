<?php
class MemberDAO extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function getUser($user_id){
		$this->db->where('userID',$user_id)->from('member');

		if($this->db->count_all_results()==0) return new Member();

		$query = $this->db->get();
		return $this->_makeMember($query->row());
	}

	function updateUser($user,$original){
		$data = $this->_preProcessUser($user);

		$user->setPassword(md5($user->getPassword()));

		if($user->getUserID()!=$original->getUserID()) return -1;
		$record = $this->db->where('nickname',$user->getNickname())->get('member')->row();
		if($record->userID!=$original->getUserID() && $record->nickname == $user->getNickname()) return -2;

		$this->db->where('userID',$user->getUserID())->update('member',$user);
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

		$query = $this->db->get();
		return $this->_makeMember($query->row());
	}

	function insertUser($data){
		$data = $this->_preProcessUser($data);

		$existUserID = $this->db->where('userID',$user->getUserID())->from('member')->count_all_results();
		if($existUserID) return -1;
		$existNickname = $this->db->where('nickname',$user->getNickname())->from('member')->count_all_results();
		if($existNickname) return -2;

		$user->setPassword(md5($user->getPassword()));
		$this->db->set($user);
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
		return $member;
	}

	function _preProcessRoom($user){
		$user->setUserID(trim($user->getUserID()));
		$user->setNickname(trim($user->getNickName()));
		return $user;
	}
}
?>