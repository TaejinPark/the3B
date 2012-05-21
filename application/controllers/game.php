<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(APPPATH.'models/room.php');
include_once(APPPATH.'models/member.php');

class Game extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index($room_seq) {
		$this->load->model('RoomDAO');
		$room = $this->RoomDAO->getRoom($room_seq);
		if($room->getRoomSeq()!=$room_seq){
			$this->load->helper('url');
			redirect('/roomlist/');
			return;
		}

		$data = array();
		$data['room'] = $room;
		$data['member'] = $this->session->userdata('member');

		$this->load->view('playroom',$data);
	}
}

?>