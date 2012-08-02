<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils {

	public function isServerOn(){
		// check that server is working
		$address="115.68.23.155";
		$port = 4279 ;
		$socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		if (socket_connect($socket, $address, $port)){ // server is working
			socket_close($socket);
			return true ;
		}
		else { // server is not working
			return false;
		}
	}
}
?>