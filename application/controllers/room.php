<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Room extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index($room_seq) {
		$this->load->view('playroom');
	}
}

?>