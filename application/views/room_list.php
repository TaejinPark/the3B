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
		<script src ="/resource/js/T3B.js"></script>
		<link 	rel="stylesheet" href="/resource/css/T3B.css">
		<script>
		$(document).ready(function(){
			loadRoomList(0);
			$('select[name=select_game_type]').change(function(){loadRoomList(0);});
			$('span.reload').click(function(){loadRoomList(0);});
			$('#makeroom').click(function(){makeRoom($(this).parent());});
		});
		</script>
	</head>
	
	<body id="body">
		
	<div data-role="page" class="type-interior">
	
		<!-- /header -->
		<div data-role="header" data-position="fixed" data-theme="a" >
			<div data-role="navbar" data-iconpos="right">
				<ul class="ui-body ui-body-b">
					<li><a data-theme="a" data-icon="search" class="ui-btn-active" onclick="view_room_list('roomlist')">Rooms</a></li>
					<li><a data-theme="a" data-icon="info" onclick="view_room_list('status')">Status</a></li>
					<li><a data-theme="a" data-icon="delete" onclick="doLogout();">Logout</a></li>
				</ul>
				
			</div>
		</div><!-- /header -->
		
		<!-- /content -->
		<div id="content" data-role="content" data-theme="a" style="padding:0px" width="100%"> 
		
			<div id="roomlist" style="display:block;">
				<div class="ui-grid-a" style="border:0px; padding:0px; margin:3px;">	
					<div id="select_game_type" class="ui-block-a" style="margin-right:3px;">
						<select name="select_game_type" data-native-menu="false">
							<option value="0" selected="selected">빙고</option>
							<option value="1">주사위</option>
							<option value="2">사다리</option>
							<option value="3">해적</option>
						</select>
					</div>
					<div id="make_icon" class="ui-block-b" align="right" onclick="view_room_list('makeroom')">
						<span data-role="button" data-inline="true" data-theme="a" data-icon="plus" style="padding:0px;margin:0px;">
						방 생성
						</span>
					</div>
				</div>
				
				<!--  -->
				
				<div class="ui-grid-a" style="border:0px; padding:0px; margin:3px;">	
					<div id="search_rooms" class="ui-block-a" style="margin-right:3px; padding-top:2px;">
						<input type="search" name="search" id="searc-basic" value="" onkeypress=""/>
					</div>
					<div id="refresh_icon" class="ui-block-b" align="right">
						<span class="reload" data-role="button" data-inline="true" data-theme="a" data-icon="refresh" style="padding:0px;margin:0px;">
						방 갱신
						</span>
					</div>
				</div>
				
				<!--  -->
				
				<div data-role="collapsible-set" data-theme="a" data-content-theme="e" style="padding:0px 9px; margin:0px;">
					<div id="RoomList">
					</div>
				</div>
				<a id="morerooms" data-role="button" data-icon="arrow-d">더보기</a>
			</div><!-- / room -->
			
			<!-- /status -->
			<div id="status" style="display:none;">
				
				<div style="border:solid 1px black; margin:30px; padding:10px;">
					<p style="margin:5px;">사용자 ID : <span id="statusUserID"></span></p>
					<p style="margin:5px;">별칭 : <span id="statusNickname"></span></p>
					<!--p style="margin:5px;">생년월일 : <span>2012</span>.<span>12</span>.<span>31</span></p-->
					<p style="margin:5px;">전/승/패 : <span id="statusTotal"></span>/<span id="statusWin"></span>/<span id="statusLose"></span></p>
				</div>
				
				<div data-role="collapsible" data-collapsed="true" data-theme="a" data-content-theme="a" style="margin:20px;">
						<h3>
							게임 통계
						</h3>
						<p>
							<table id="statistics">
								<tr>
									<th>게임 명</th>
									<th>게임 횟수</th>
									<th>승리 횟수</th>
									<th>패배 횟수</th>
								</tr>
								<tr>
									<td>빙고</td>
									<td id="bingoTotal">0</td>
									<td id="bingoWin">0</td>
									<td id="bingoLose">0</td>
								</tr>
							</table>
					 	</p>
				</div>
				<!--div data-role="button" data-inline="true" data-theme="a" data-icon="refresh" style="margin-left:10px; padding:0px;">
				탈퇴
				</div-->
				
			</div><!-- /status -->

			<!-- /make room -->
			<div id="makeroom" style="display:none;">
				<input type="hidden" name="gametype" value="0" />
				<div style="margin:20px;">
					<center>게임 방 새로 만들기</center>
				</div>
				<div style="margin:20px; width:80%;">
					<label for="roomname">방 이름</label>
					<input type="text" id="roomname" name="name" value="" maxlength="50" data-mini="true" />
				</div>
				<div style="margin:20px;">
					<label for="maxuser">참가자</label>
					<input type="range" name="maxuser" id="maxuser" value="2" min="2" max="8" data-theme="a" data-track-theme="b"/>
				</div>
				<div style="margin:20px;float:left;">
					<label for="private">공개 / 비 공개</label>
					<select name="private" id="private" data-role="slider" data-theme="a">
						<option value="0">공개</option>
						<option value="1">비공개</option>
					</select> 
				</div>
				<div style="margin:20px 20px 20px 40px;float:left">
					<label for="roomtype">방종류</label>
					<select name="roomtype" id="roomtype" data-role="slider" data-theme="a">
						<option value="0">1회성</option>
						<option value="1">일반</option>
					</select> 
				</div>
				<div style="margin:20px; width:80%; clear:both;">
					<label for="password">비밀 번호</label>
					<input type="password" id="password" name="password" value="" maxlength="50" data-mini="true" />
				</div>
				<div style="margin:20px; width:80%;">
					<span>게임 옵션</span>
					<div style="margin-top: 10px; margin-left: 15px;">
						<label for="gameoption">승리조건 (빙고 완성 줄 수)</label>
						<input type="range" name="gameoption" id="gameoption" value="1" min="1" max="5" data-theme="a" data-track-theme="b"/>
					</div>
				</div>
				<div id="makeroom" type="button" data-theme="a" data-icon="plus">
					만들기
				</div>
			</div><!-- /make room -->
			
			
		</div><!-- /content -->
		
		<!-- /footer -->	
		<div data-role="footer" data-position="fixed" data-theme="a">
			<center id="topbtm">
				<a type="button" data-icon="arrow-u" data-iconpos="notext">Top</a>
				<a type="button" data-icon="arrow-d" data-iconpos="notext">Bottom</a>
			</center>
		</div><!-- /footer -->
		
	</div><!-- /page -->
	</body>
	
</html>

		
