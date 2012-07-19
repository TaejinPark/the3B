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

		$query = $this->db->where('room_seq',$room_seq)->get('room');
		$data = $query->row();

		$room->setRoomSeq($data->room_seq);
		$room->setName($data->name);
		$room->setMaxUser($data->maxuser);
		$room->setPrivate($data->private);
		$room->setPassword($data->password);
		$room->setGameType($data->gametype);
		$room->setGameOption(unserialize($data->gameoption));
		$room->setRoomType($data->roomtype);
		$room->setOwner($data->owner);

		return $room;
	}

	function makeRoom($data){//receive Room instance
		$room = $this->_preProcessRoom($data);

		if($room->getName()=="" || !$room->getName()) return -1;
		
		$room->setStart(0);
		unset($room->currentuser);
		$this->db->set($room);
		$this->db->insert('room');

		if($this->db->affected_rows()>0) return $this->db->insert_id();
		else return 0;
	}

	function updateRoom($data,$original){
		$room = $this->_preProcessRoom($data);

		if($room->getName()=="" || !$room->getName()) return -1;

		$room->setRoomSeq($original->getRoomSeq());
		unset($room->currentuser);
		
		$this->db->where('room_seq',$room->getRoomSeq())->update('room',$room);
		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function getRoomList($start=0, $limit=15, $keyword="",$type=0){ // 데이터 베이스로부터 조건에 따른 방 목록을 구함
		if($keyword) $this->db->like('name',trim($keyword)); // 검색 키워드가 있으면 키워드 검색 조건에 추가
		if($type)$this->db->where('gametype',(int)($type)); // 검색 키워드로 게임 형식이 있으면 검색 조건에
		$result = $this->db->get('room',$limit,$start); // 쿼리 전송 및 $result에 쿼리 결과 저장

		$data = array();
		foreach( $result->result() as $row){ // 쿼리로 날아온 데이터를 Room instance로 변환 및 저장
			$tmp = new Room();					// Room instance 생성
			$tmp->setRoomSeq($row->room_seq); 	// 방 번호
			$tmp->setName($row->name); 			// 방 이름
			$tmp->setMaxUser($row->maxuser);	// 게임 참가 최대 인원
			$tmp->setPrivate($row->private);	// 비공개 여부
			$tmp->setPassword($row->password);	// 비밀번호 여부
			$tmp->setGameType($row->gametype);	// 게임 종류
			$tmp->setGameOption($row->gameoption);	// 게임 옵션
			$tmp->setRoomType($row->roomtype);	// 임시방, 일반방
			$tmp->setOwner($row->owner);		// 방장 정보
			$data[] = $tmp;						// Room instance 를 배열에 저장
		}

		for($a=0,$loopa=sizeof($data); $a<$loopa; $a++){ // 각 방에 접속해 있는 사용자의 숫자를 구하여 Room instance에 저장
			$tmp2 = $this->db->select('count(*) as `cnt`',false)->where('room_seq',$data[$a]->getRoomSeq())->group_by('room_seq')->get('room_user'); // 방에 접속한 사용자들을 구함
			if($tmp2->num_rows()>0){
				$tmp = $tmp2->row();
				$data[$a]->setCurrentUser($tmp->cnt); // the number of users in the room  save into room instance.사용자의 수를 Room instance 에 저장
			}
			else 
			{
				$data[$a]->SetCurrentUser(0); // if there is no user in the room , user number value of room configuration is set to zero.
				
				/*
				// 방이 없으므로 방을 데이터베이스와 Room instance array 에서 삭제
				code 추가

				*/
			}
		}
		return $data; // return the Romm instance array
	}

	function updateOwner($room_seq, $owner){
		$this->db->set('owner',$owner)->where('room_seq',$room_seq)->update('room');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function destroyRoom($room_seq){
		$this->db->where('room_seq',$room_seq)->delete('room');

		$return = $this->db->affected_rows();

		$this->db->where('room_seq',$room_seq)->delete('room_user');

		if($return>0) return 1;
		else return 0;
	}

	function startRoom($room_seq){
		$this->db->set('start',1)->where('room_seq',$room_seq)->update('room');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function stopRoom($room_seq){
		$this->db->set('start',0)->where('room_seq',$room_seq)->update('room');

		if($this->db->affected_rows()>0) return 1;
		else return 0;
	}

	function _preProcessRoom($room){
		$room->setName(trim($room->getName())); // eliminate blank or escape character like "\0 , \r , ' ' "
		if($room->getPassword()) 
			$room->setPassword(md5($room->getPassword()));
		$room->setGameOption(serialize($room->getGameOption()));
		return $room;
	}
}
?>