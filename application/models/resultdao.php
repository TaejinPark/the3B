<?php
include_once(APPPATH.'models/result.php');

class ResultDAO extends CI_Model {

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function getResultList($userID, $type=0){
		$result = $this->db->where("userID",$userID)->where("type",$type)->get("result");

		$dataList = array();
		foreach($result->result() as $data){
			$tmp = new Result();
			$tmp->setSeq($data->seq);
			$tmp->setUserID($data->userID);
			$tmp->setGameType($data->gametype);
			$tmp->setGameOption($data->gameoption);
			$tmp->setTime($data->time);
			$tmp->setResult($data->result);
			$dataList[] = $tmp;
		}
		return $dataList;
	}

	function insertResult($userID, $data){
		$data->setSeq(null);

		if(!$data->getUserID()) return -1;
		$this->load->model('MemberDAO');
		$tmp = $this->MemberDAO->getUser($data->getUserID());
		if($tmp->getUserID() != $data->getUserID()) return -2;

		$this->db->set($data)->insert('result');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function getResult($userID, $type=0){
		$result = $this->db->select("(select count(*) from `result` where `result`=1 and `gametype`={$type} and `userID`='{$userID}' group by `result` ) as `win`",false)
			 ->select("(select count(*) from `result` where `userID`='{$userID}' and `gametype`={$type} group by `userID` ) as `total`",false)->get();
		$data = array();
		foreach( $result->result() as $row){
			$data[] = $row;
		}
		return $data;
	}
}
?>