<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		//load MainPage
		$this->load->view('index');
	}

	function doLogin(){
		$id = $this->input->post('userID');
		$password = $this->input->post('password');

		$this->load->model('MemberDAO');
		$member = $this->MemberDAO->getUser($id);

		if($member->getUserID() != $id || $member->getPassword() != md5($password)){
			echo 'false';
			return;
		}

		//session
		$this->session->set_userdata('member',$member);

		echo 'true';
	}

	function doJoin(){
		$id = $this->input->post('userID');
		$password = $this->input->post('password');
		$nickname = $this->input->post('nickname');
	}
}

?>