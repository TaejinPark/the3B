<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RoomList extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index(){
		//load RoomList
		$room = $this->_getRoomList();

		$data = array();
		$data['room'] = $room;

		$this->load->view('room_list',$data);
	}

	function getRoomListToJson(){
		$start = $this->input->get('start');
		$limit = $this->input->get('limit');
		$keyword = $this->input->get('keyword');
		$type = $this->input->get('type');

		$list = $this->_getRoomList($start, $limit, $keyword, $type);
		return json_encode($list);
	}

	function _getRoomList($start=0, $limit=15, $keyword="",$type=0){
		$this->load->model('RoomDAO');
		return $this->RoomDAO->getRoomList($start, $limit, $keyword, $type);
	}
}

?>