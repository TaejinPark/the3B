<!DOCTYPE html>
<html>
	<head>
		<title>The BokBulBok</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<link   href="/resource/js/jquery/jquery.mobile-1.1.0.css" rel="stylesheet"/>
		<script src ="http://code.jquery.com/jquery-1.6.4.min.js"></script>
		<script src ="/resource/js/jquery/jquery.mobile-1.1.0.min.js"></script>
		<script src ="/resource/js/playroom.js"></script>
		<link 	href="/resource/css/playroom.css" rel="stylesheet" >
		<style type="text/css">

			
		</style>
		<script type="text/javascript">

		</script>
	</head>
	
	<body id="body">
		
	<div data-role="page" class="type-interior">
	
		<!-- /header -->
		<div id="header" data-role="header" data-position="fixed" data-theme="a">
				<h3 data-inline="true">방 이름</h3>
				<div id="unfold"><a onclick="view_config('room_info'); view_folding('fold');"data-role="button" data-icon="arrow-d" data-iconpos="notext" data-theme="a">unfold</a></div>
				<div id="fold"><a onclick="view_config('none'); view_folding('unfold');" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-theme="a">fold</a></div>

		</div><!-- /header -->
		
		<!-- /content -->
		<div id="content" data-role="content" data-theme="a">
			<div id="room_info"><!-- /room inform-->
				<div>참가자 : 
					<span>6</span> / 
					<span>8</span>
				</div>
				<div>게임 종류 : 
					<span>빙고</span>
				</div>
				<div>-Option-<br>
					승리 빙고 : 
					<span>3줄</span>
				</div>
				<a id="config_change" onclick="view_config('room_config');" type="button" data-inline="true;">설정 변경</a>
			</div><!-- /room inform -->
			
			<div id="room_config"><!-- /room config-->
				<div>
					<div id="participant_num" data-role="fieldcontain">
						<div>참가자</div>
					 	<input type="range" name="slider" id="slider-a" value="2" min="2" max="8" data-theme="e"/>
					</div>
					<div id="select_game_type">
						<div>게임 종류</div>
						<select name="select_game_type" data-native-menu="false">
							<option value="bingo">빙고</option>
							<option value="dice">주사위</option>
							<option value="ladder">사다리</option>
							<option value="pirate">해적</option>
						</select>
					</div>
					<div id="bingo_option_line"data-role="fieldcontain">
						<div>빙고 라인 갯수</div>
					 	<input type="range" name="slider" id="slider-a" value="2" min="2" max="8" data-theme="e"/>
					</div>
					<a id="config_confirm" type="button" data-inline="true;">적용</a>
				</div>
			</div><!-- /room config -->
				
			<div id="chat">
					대화 내용
			</div>
			
			<div id="participant_list">
				<a type="button" data-inline="true">방장</a>
				<a id="ready3" type="button" data-inline="true">시작 요청</a>
				<span id="host">사용자1</span>
				<br>
				<a id="kick2" type="button" data-inline="true">강퇴</a>
				<a id="ready2" type="button" data-inline="true">준비 요청</a>
				<span id="user2" >사용자2</span><br>
				
				<a id="kick3" type="button" data-inline="true">강퇴</a>
				<a id="ready3" type="button" data-inline="true">준비 요청</a>
				<span id="user3" >사용자3</span><br>
				
				<a id="kick4" type="button" data-inline="true">강퇴</a>
				<a id="ready4" type="button" data-inline="true">준비 요청</a>
				<span id="user4" >사용자4</span><br>
				
				<a id="kick5" type="button" data-inline="true">강퇴</a>
				<a id="ready5" type="button" data-inline="true">준비 요청</a>
				<span id="user5" >사용자5</span><br>
				
				<a id="kick6" type="button" data-inline="true">강퇴</a>
				<a id="ready6" type="button" data-inline="true">준비 요청</a>
				<span id="user6" >사용자6</span><br>
				
				<a id="kick7" type="button" data-inline="true">강퇴</a>
				<a id="ready7" type="button" data-inline="true">준비 요청</a>
				<span id="user7" >사용자7</span><br>
				
				<a id="kick8" type="button" data-inline="true">강퇴</a>
				<a id="ready8" type="button" data-inline="true">준비 요청</a>
				<span id="user8" >사용자8</span><br>
			</div>
			
			<div id="gamedisplay">
				<div>
					
				</div>
			</div>
			
		</div><!-- /content -->
		<!-- /footer -->	
		<div id="footer" data-role="footer" data-position="fixed" data-theme="a">
			<div id="chat_input_form">
				<div id="chat_input"><input type="text"></div>
				<div id="chat_send" ><a type="button">전송</a></div>
			</div>
			<div id="divider"></div>
			<div>
				<div id="chat_list_button"><a type="button" onclick="view('chat')">채팅창</a></div>
				<div id="participant_button"><a type="button" onclick="view('participant_list')">참가자</a></div>
				<div id="play_button">
					<a type="button" href="askplay.html" data-rel="dialog">Play</a>
				</div>
				<div id="exit_button">
					<a type="button" href="askexit.html" data-rel="dialog">Exit</a>
				</div>
			</div>
		</div><!-- /footer -->
	</div><!-- /page -->
	</body>
	
</html>

		