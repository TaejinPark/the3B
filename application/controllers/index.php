<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(APPPATH.'models/member.php');

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

		$member = new Member();
		$member->setUserID($id);
		$member->setPassword($password);
		$member->setNickname($nickname);

		$this->load->model('MemberDAO');
		$result = $this->MemberDAO->insertUser($member);
		if($result==1) echo 'true';
		else echo 'false';
	}

	function isExistID(){
		$id = $this->input->post('userID');

		$this->load->model('MemberDAO');
		$member = $this->MemberDAO->getUser($id);

		if($member->getUserID()) echo 'false';
		else echo 'true';
	}

	function isExistNickname(){
		$nickname = $this->input->post('nickname');

		$this->load->model('MemberDAO');
		$member = $this->MemberDAO->getUserByNickname($nickname);

		if($member->getUserID()) echo 'false';
		else echo 'true';
	}
}

?>