<?php
include_once(APPPATH.'models/member.php');
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

define("__SERVER__","115.68.23.155");
define("__PORT__",4279);

class Server extends CI_Controller {
	private $master;
	private $sockets;
	private $users = array();
	private $debug = false;
	private $roomusers = array();
	private $roomstatus = array();
	private $roombingo = array();
	private $roomcurrent = array();
	private $roomend = array();

	function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function start(){
		$this->master = $this->WebSocket(__SERVER__,__PORT__);
		$this->sockets = array($this->master);

		while(true){
			$changed = $this->sockets;
			socket_select($changed,$write=NULL,$except=NULL,1);
			foreach($changed as $socket){
				if($socket == $this->master){
					$client = socket_accept($this->master);
					if($client<0) { $this->console("socket_accept() failed"); continue; }
					else { $this->connect($client); }
				} else {
					$bytes = @socket_recv($socket,$buffer,2048,0);
					if($bytes!=0){
						$user = $this->getuserbysocket($socket);
						if(!$user->handshake) { $this->dohandshake($user,$buffer); }
						else { $this->process($user,$buffer); }
					} else if($bytes==0) {
						$this->disconnect($socket);
					}
				}
			}
		}
	}

	function process(&$user,$msg){
		$action = $this->parseMessage($this->unwrap($msg));
		$this->say("Recv> ".$action->cmd.' '.print_r($action->data,true));
		$returnObject->cmd = '';
		switch($action->cmd){
			case 'LOGIN':
				if(!isset($action->data->Sessionid) || !isset($action->data->UserID) || !$action->data->Sessionid || !$action->data->UserID)
					return $this->sendError($user->socket,$action->cmd,101);
				else {
					$this->load->model('MemberDAO');
					$member = $this->MemberDAO->getUserBySessionID($action->data->Sessionid);
					if(!$member->getUserID() || $member->getUserID()!=$action->data->UserID)
						return $this->sendError($user->socket,$action->cmd,101);
					$returnObject->cmd = 'OK';
					$returnObject->data = '';
					$this->say("Proc> ".$action->cmd.':: OK - '.$member->getUserID());
					$user->member = $member;
				}
				break;
			case 'JOIN':
				$this->load->model('RoomUserDAO');
				if($this->RoomUserDAO->existsUser($user->member->getUserID()))
					return $this->sendError($user->socket,$action->cmd,103);

				$this->load->model('RoomDAO');
				$room = $this->RoomDAO->getRoom($action->data->room_seq);
				if(!$room->getRoomSeq() || $room->getRoomSeq() != $action->data->room_seq)
					return $this->sendError($user->socket,$action->cmd,102);

				if($room->getMaxUser()== $room->getCurrentUser())
					return $this->sendError($user->socket,$action->cmd,119);

				$this->RoomUserDAO->joinRoom($room->getRoomSeq(),$user->member->getUserID(),(substr($user->member->getUserID(),0,7)=="__Guest"?1:0));
				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK - '.$user->member->getUserID());
				$user->room = $room;
				if(!isset($this->roomusers[$room->getRoomSeq()]))
					$this->roomusers[$room->getRoomSeq()] = array();
				else {
					$data->UserID = $user->member->getUserID();
					$data->Nickname = $user->member->getNickname();
					$this->sendAllUser($room->getRoomSeq(),"JOIN",$data);
				}
				$this->roomusers[$room->getRoomSeq()][] = $user;
				$user->ready = false;
				break;
			case 'USERLIST':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);
				$this->load->model('RoomUserDAO');
				$list = $this->RoomUserDAO->getUserList($user->room->getRoomSeq());
				$tmp = array();
				$this->load->model('MemberDAO');
				for($a=0,$loopa=sizeof($list); $a<$loopa; $a++){
					$obj->UserID = $list[$a]->getUserID();
					if($list[$a]->getAnonymous())
						$m = $list[$a]->getUserID();
					else {
						$m = $this->MemberDAO->getUser($list[$a]->getUserID());
						$m = $m->getNickname();
					}
					$obj->Nickname = $m;
					$tmp[] = $obj;
					unset($obj);
				}
				$returnObject->cmd = "USERLIST";
				$returnObject->data = $tmp;
				$this->say("Proc> ".$action->cmd.':: OK - '.$user->room->getRoomSeq());
				break;
			case 'CHAT':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);

				$data->Message = $action->data->Message;
				$data->Nickname = $user->member->getNickname();
				$this->sendAllUser($user->room->getRoomSeq(),"CHAT",$data);

				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK '.$action->data->Message);
				break;
			case 'READY':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);
				if($user->ready)
					return $this->sendError($user->socket,$action->cmd,104);
				if($user->member->getUserID()==$user->room->getOwner())
					return $this->sendError($user->socket,$action->cmd,105);
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);

				$user->ready = true;
				$this->load->model('RoomUserDAO');
				$this->RoomUserDAO->updateReady($user->room->getRoomSeq(),$user->member->getUserID(),1);

				$data->Nickname = $user->member->getNickname();
				$this->sendAllUser($user->room->getRoomSeq(),"READY",$data);

				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK ');
				break;
			case 'UNREADY':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);
				if(!$user->ready)
					return $this->sendError($user->socket,$action->cmd,106);
				if($user->member->getUserID()==$user->room->getOwner())
					return $this->sendError($user->socket,$action->cmd,107);
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);

				$user->ready = false;
				$this->RoomUserDAO->updateReady($user->room->getRoomSeq(),$user->member->getUserID(),0);

				$data->Nickname = $user->member->getNickname();
				$this->sendAllUser($user->room->getRoomSeq(),"READY",$data);

				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK ');
				break;
			case 'START':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);

				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					if(!$this->roomusers[$user->room->getRoomSeq()][$a]->ready)
					return $this->sendError($user->socket,$action->cmd,108);
				}
				if($user->member->getUserID()!=$user->room->getOwner())
					return $this->sendError($user->socket,$action->cmd,109);
				if(sizeof($this->roomusers[$user->room->getRoomSeq()])==1)
					return $this->sendError($user->socket,$action->cmd,110);
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);

				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					$this->roomusers[$user->room->getRoomSeq()][$a]->bingo_end = false;
					$this->roomusers[$user->room->getRoomSeq()][$a]->bingo = 0;
					$this->roomusers[$user->room->getRoomSeq()][$a]->result = -1;
				}
				$this->roombingo[$user->room->getRoomSeq()] = array();
				$this->roomcurrent[$user->room->getRoomSeq()] = 1;
				$this->roomend[$user->room->getRoomSeq()] = 0;

				$this->roomstatus[$user->room->getRoomSeq()] = true;

				$this->sendAllUser($user->room->getRoomSeq(),"START","");

				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK ');
				break;
			case 'KICK':
				if(!$user->room->getRoomSeq())
					return $this->sendError($user->socket,$action->cmd,116);

				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					if(!$this->roomusers[$user->room->getRoomSeq()][$a]->ready)
					return $this->sendError($user->socket,$action->cmd,108);
				}
				if($user->member->getUserID()!=$user->room->getOwner())
					return $this->sendError($user->socket,$action->cmd,109);
				if(sizeof($this->roomusers[$user->room->getRoomSeq()])==1)
					return $this->sendError($user->socket,$action->cmd,110);
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);

				$flag = false;
				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					if($this->roomusers[$user->room->getRoomSeq()][$a]->member->getUserID()==$action->data->UserID){
						$this->load->model('RoomUserDAO');
						$this->RoomUserDAO->kickUser($user->room->getRoomSeq(),$this->roomusers[$user->room->getRoomSeq()][$a]->member->getUserID(),(substr($this->roomusers[$user->room->getRoomSeq()][$a]->member->getUserID(),0,7)=="__Guest"?1:0));
						unset($this->roomusers[$user->room->getRoomSeq()][$a]);
						$flag = true;
						break;
					}
				}
				if(!$flag)
					return $this->sendError($user->socket,$action->cmd,118);

				$data->UserID = $action->data->UserID;
				$this->sendAllUser($user->room->getRoomSeq(),"KICK",$data);

				$returnObject->cmd = 'OK';
				$returnObject->data = '';
				$this->say("Proc> ".$action->cmd.':: OK ');
				break;
			case 'BINGO_START':
				$this->sendAllUser($user->room->getRoomSeq(),"BINGO_START","");
				break;
			case 'BINGO_WRITED':
				$user->bingo_end = true;

				$flag = true;
				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					if(!$this->roomusers[$user->room->getRoomSeq()]->bingo_end){
						$flag = false;
						break;
					}
				}

				if($flag)
					$this->sendAllUser($user->room->getRoomSeq(),"BINGO_START","");
				break;
			case 'BINGO_SELECT':
				if(in_array($action->data->SelectedNumber,$this->roombingo[$user->room->getRoomSeq()]))
					return $this->sendError($user->socket,$action->cmd,201);

				$this->roomcurrent[$user->room->getRoomSeq()] = $this->roomend[$user->room->getRoomSeq()]+1;

				$data->Nickname = $user->member->getNickname();
				$data->SelectedNumber = $action->data->SelectedNumber;
				$this->sendAllUser($user->room->getRoomSeq(),"BINGO_SELECT",$data);
				break;
			case 'BINGO_BINGO':
				if($user->bingo>= $user->room->getGameOption)
					return $this->sendError($user->socket,$action->cmd,202);

				$user->bingo++;
				$data->Nickname = $user->member->getNickname();
				if($user->bingo>= $user->room->getGameOption()){
					$this->roomend[$user->room->getRoomSeq()]++;
					$user->result = $this->roomcurrent[$user->room->getRoomSeq()];
					$this->sendAllUser($user->room->getRoomSeq(),"BINGO_LAST",$data);
				} else
					$this->sendAllUser($user->room->getRoomSeq(),"BINGO_BINGO",$data);

				if($this->roomend[$user->room->getRoomSeq()]==sizeof($this->roomusers[$user->room->getRoomSeq()])){
					$list = array();
					for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
						$tmp->Nickname = $this->roomusers[$user->room->getRoomSeq()][$a]->member->getNickname();
						$tmp->result = $this->roomusers[$user->room->getRoomSeq()][$a]->result;
						$list[] = $tmp;
					}
					unset($data);
					$data->result = $list;
					$this->sendAllUser($user->room->getRoomSeq(),"BINGO_END",$data);
					$this->sendAllUser($user->room->getRoomSeq(),"INSTANCE_EXIT","");
				}
				break;
			case 'QUIT':
				if($this->roomstatus[$user->room->getRoomSeq()])
					return $this->sendError($user->socket,$action->cmd,117);

				$this->load->model('RoomUserDAO');
				$this->RoomUserDAO->leaveRoom($user->room->getRoomSeq(),$user->member->getUserID(),(substr($user->member->getUserID(),0,7)=="__Guest"?1:0));

				$data->Nickname = $user->member->getNickname();
				$this->sendAllUser($user->room->getRoomSeq(),"QUIT",$data);

				$room = $user->room;

				for($a=0,$loopa=sizeof($this->roomusers[$user->room->getRoomSeq()]); $a<$loopa; $a++){
					if($user->id == $this->roomusers[$user->room->getRoomSeq()][$a]){
						unset($this->roomusers[$user->room->getRoomSeq()][$a]);
						break;
					}
				}

				if($user->member->getUserID()==$room->getOwner()){
					if(sizeof($this->roomusers[$room->getRoomSeq()])==0){
						$this->load->model('RoomDAO');
						$this->RoomDAO->destroyRoom($room->getRoomSeq());
						unset($this->roomusers[$room->getRoomSeq()]);
						unset($this->roomstatus[$room->getRoomSeq()]);
						unset($this->roombingo[$room->getRoomSeq()]);
						unset($this->roomcurrent[$room->getRoomSeq()]);
						unset($this->roomend[$room->getRoomSeq()]);
					} else {
						$data->Nickname = $this->roomusers[$room->getRoomSeq()][0]->member->getNickname();
						$this->load->model('RoomDAO');
						$this->RoomDAO->updateOwner($room->getRoomSeq(),$this->roomusers[$room->getRoomSeq()][0]->member->getUserID());
						$this->sendAllUser($room->getRoomSeq(),"CHANGE_OWNER",$data);
					}
				}
				break;
			case 'CHANGE_SETTING':
				$data->cmd = $action->cmd;
				$data->data = $action->data;
				$this->sendAllUser($user->room->getRoomSeq(),"CHANGE_OWNER",$data);

				$room = $user->room;
				$room->setMaxUser($data->data->MaxUser);
				$room->setGameOption($data->data->GameOption);

				$this->load->model('RoomDAO');
				$this->RoomDAO->updateRoom($room,$room);
				break;
			default: $this->say("Proc> ".$action->cmd.' invaild'); break;
		}
		if($returnObject->cmd)
			return $this->sendMessage($user->socket,$returnObject);
	}

	function parseMessage($msg){
		return json_decode($msg);
	}

	function sendMessage($client,$data){
		return $this->send($client,json_encode($data));
	}

	function sendError($client,$precommand,$errorcode){
		$returnObject->cmd = 'ERROR';
		$returnObject->data = $errorcode;
		$this->say("Proc> ".$precommand.':: ERROR '.$errorcode);
		return $this->sendMessage($client,$returnObject);
	}

	function sendAllUser($room_seq,$command,$data){
		$msgObject->cmd = $command;
		$msgObject->data = $data;
		$this->say("Proc> SEND ALLUSER:: ".$command." - ".print_r($data,true));
		for($a=0,$loopa=sizeof($this->roomusers[$room_seq]); $a<$loopa; $a++){
			$this->sendMessage($this->roomusers[$room_seq][$a]->socket,$msgObject);
		}
	}

	function send($client,$msg){
		$this->say("Send> ".$msg);
		$msg = $this->wrap($msg);
		socket_write($client,$msg,strlen($msg));
	}

	function WebSocket($address,$port){
		$master=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)     or die("socket_create() failed");
		socket_set_option($master, SOL_SOCKET, SO_REUSEADDR, 1)  or die("socket_option() failed");
		socket_bind($master, $address, $port)                    or die("socket_bind() failed");
		socket_listen($master,20)                                or die("socket_listen() failed");
		echo "Server Started : ".date('Y-m-d H:i:s')."\n";
		echo "Master socket  : ".$master."\n";
		echo "Listening on   : ".$address." port ".$port."\n\n";
		return $master;
	}

	function connect($socket){
		$user = new User();
		$user->id = uniqid();
		$user->socket = $socket;
		array_push($this->users,$user);
		array_push($this->sockets,$socket);
		$this->console($socket." CONNECTED!");
	}

	function disconnect($socket){
		$found = null;
		$n = count($this->users);
		for($i=0;$i<$n;$i++){
			if($this->users[$i]->socket==$socket) { $found=$i; break; }
		}
		$user = $this->users[$found];
		$idx = array_search($socket,$this->sockets);
		socket_close($socket);
		array_splice($this->sockets,$idx,1);
		$this->console($socket." DISCONNECTED!");
		if(!is_null($found)) {
			if(isset($this->users[$found]->member)&&isset($this->users[$found]->room)){
				$this->load->model('RoomUserDAO');
				$this->RoomUserDAO->leaveRoom($this->users[$found]->room->getRoomSeq(),$this->users[$found]->member->getUserID(),(substr($this->users[$found]->member->getUserID(),0,7)=="__Guest"?1:0));
				$idx = null;
				for($a=0,$loopa=sizeof($this->roomusers[$this->users[$found]->room->getRoomSeq()]); $a<$loopa; $a++){
					if($this->roomusers[$this->users[$found]->room->getRoomSeq()][$a]->socket==$socket) { $idx = $a; break; }
				}
				array_splice($this->roomusers[$this->users[$found]->room->getRoomSeq()],$idx,1);
			}
			$obj->Nickname = $user->member->getNickname();
			$this->sendAllUser($user->room->getRoomSeq(),"QUIT",$obj);
			array_splice($this->users,$found,1);
			$this->users[$found] = null;
		}
	}

	function dohandshake($user,$buffer){
		$this->console("\nRequesting handshake...");
		$this->console($buffer);
		list($resource,$host,$origin,$strkey1,$strkey2,$data) = $this->getheaders($buffer);
		$this->console("Handshaking...");

		$pattern = '/[^\d]*/';
		$replacement = '';
		$numkey1 = preg_replace($pattern, $replacement, $strkey1);
		$numkey2 = preg_replace($pattern, $replacement, $strkey2);

		$pattern = '/[^ ]*/';
		$replacement = '';
		$spaces1 = strlen(preg_replace($pattern, $replacement, $strkey1));
		$spaces2 = strlen(preg_replace($pattern, $replacement, $strkey2));

		if ($spaces1 == 0 || $spaces2 == 0 || $numkey1 % $spaces1 != 0 || $numkey2 % $spaces2 != 0) {
			socket_close($user->socket);
			$this->console('failed');
			return false;
		}

		$ctx = hash_init('md5');
		hash_update($ctx, pack("N", $numkey1/$spaces1));
		hash_update($ctx, pack("N", $numkey2/$spaces2));
		hash_update($ctx, $data);
		$hash_data = hash_final($ctx,true);

		$upgrade  = "HTTP/1.1 101 WebSocket Protocol Handshake\r\n" .
				  "Upgrade: WebSocket\r\n" .
				  "Connection: Upgrade\r\n" .
				  "Sec-WebSocket-Origin: " . $origin . "\r\n" .
				  "Sec-WebSocket-Location: ws://" . $host . $resource . "\r\n" .
				  "\r\n" .
				  $hash_data;

		socket_write($user->socket,$upgrade.chr(0),strlen($upgrade.chr(0)));
		$user->handshake=true;
		$this->console($upgrade);
		$this->console("Done handshaking...");
		return true;
	}

	function getheaders($req){
		$r=$h=$o=null;
		if(preg_match("/GET (.*) HTTP/"   ,$req,$match)){ $r=$match[1]; }
		if(preg_match("/Host: (.*)\r\n/"  ,$req,$match)){ $h=$match[1]; }
		if(preg_match("/Origin: (.*)\r\n/",$req,$match)){ $o=$match[1]; }
		if(preg_match("/Sec-WebSocket-Key2: (.*)\r\n/",$req,$match)){ $key2=$match[1]; }
		if(preg_match("/Sec-WebSocket-Key1: (.*)\r\n/",$req,$match)){ $key1=$match[1]; }
		if(preg_match("/\r\n(.*?)\$/",$req,$match)){ $data=$match[1]; }
		return array($r,$h,$o,$key1,$key2,$data);
	}

	function &getuserbysocket($socket){
		$found=null;
		foreach($this->users as $user){
			if($user->socket==$socket){ $found=$user; break; }
		}
		return $found;
	}

	function say($msg="") { echo $msg."\n"; }
	function wrap($msg="") { return chr(0).$msg.chr(255); }
	function unwrap($msg="") { return substr($msg,1,strlen($msg)-2); }
	function console($msg="") { if($this->debug){ echo $msg."\n"; } }
}

class User{
	var $id;
	var $socket;
	var $handshake;
	var $member;
}



?>