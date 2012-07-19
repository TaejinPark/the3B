<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(APPPATH.'models/member.php');

class Index extends CI_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		//load MainPage
		$this->load->view('index');
	}

	function doLogin(){
		$data = $this->session->userdata('member');
		if($data){
			echo 'existslogin';
			return;
		}

		$id = $this->input->post('userID');
		$password = $this->input->post('password');

		$this->load->model('MemberDAO');
		$member = $this->MemberDAO->getUser($id);

		if($member->getUserID() != $id || $member->getPassword() != md5($password)){
			echo 'false';
			return;
		}

		//session
		$this->MemberDAO->updateSessionID($member->getUserID(),$this->session->userdata('session_id'));
		$member->setSessionID($this->session->userdata('session_id'));
		$this->session->set_userdata('member',$member);

		echo 'true';
	}

	function doJoin(){
		$data = $this->session->userdata('member');
		if($data){
			echo 'existslogin';
			return;
		}

		$id = $this->input->post('userID');
		$password = $this->input->post('password');
		$nickname = $this->input->post('nickname');

		$member = new Member();
		$member->setUserID($id);
		$member->setPassword($password);
		$member->setNickname($nickname);

		$this->load->model('MemberDAO');
		$result = $this->MemberDAO->insertUser($member);
		if($result==1)	echo 'true';
		else 			echo 'false';
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

	function doGuestLogin(){
		$data = $this->session->userdata('member');
		if($data){
			echo 'existslogin';
			return;
		}

		$member = new Member();
		$member->setUserID('__Guest'.strtoupper(substr(md5(rand()),0,6)));
		$member->setNickname($member->getUserID());
		$this->session->set_userdata('member',$member);
		echo 'true';
	}
}

?>