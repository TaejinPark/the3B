<?php
include_once(APPPATH.'models/room.php');

class RoomDAO extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function getRoom($room_seq){
		$this->db->where('room_seq',$room_seq)->from('room');
		
		$room = new Room();
		if($this->db->count_all_results()==0) return new $room;

		$query = $this->db->get();
		$data = $query->row();

		$room->setRoomSeq($data->room_seq);
		$room->setName($data->name);
		$room->setMaxUser($data->maxuser);
		$room->setPrivate($data->private);
		$room->setPassword($data->password);
		$room->setGameType($data->gmaetype);
		$room->setGameOption($data->gameoption);
		$room->setRoomType($data->roomtype);
		$room->setOwner($data->owner);

		return $room;
	}

	function makeRoom($data){
		$room = $this->_preProcessRoom($data);

		if($room->getName()=="" || !$room->getName()) return -1;
		
		$this->db->set($room);
		$this->db->insert('room');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function updateRoom($data,$original){
		$room = $this->_preProcessRoom($data);

		if($room->getName()=="" || !$room->getName()) return -1;

		$room->setRoomSeq($original->getRoomSeq());
		
		$this->db->where('room_seq',$room->getRoomSeq())->update('room',$room);
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function getRoomList($start=0, $limit=15, $keyword="",$type=0){
		if($keyword) $this->db->like('name',trim($keyword));
		if($type) $this->db->where('gametype',(int)($type));

		$result = $this->db->get('room',$limit,$start);
		$data = array();
		foreach($result->result() as $row){
			$tmp = new Room();
			$tmp->setRoomSeq($row->room_seq);
			$tmp->setName($row->name);
			$tmp->setMaxUser($row->maxuser);
			$tmp->setPrivate($row->private);
			$tmp->setPassword($row->password);
			$tmp->setGameType($row->gmaetype);
			$tmp->setGameOption($row->gameoption);
			$tmp->setRoomType($row->roomtype);
			$tmp->setOwner($row->owner);
			$data[] = $tmp;
		}
		return $data;
	}

	function _preProcessRoom($room){
		$room->setName(trim($room->getName()));
		if($room->getPassword()) $room->setPassword(md5($room->getPassword()));
		$room->setGameOption(serialize($room->getGameOption()));
		return $room;
	}
}
?>