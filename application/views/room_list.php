<!DOCTYPE html>
<html>
	<head>
		<title>The BokBulBok</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="/resource/js/jquery/jquery.mobile-1.1.0.css" rel="stylesheet"/>
		<script src ="http://code.jquery.com/jquery-1.6.4.min.js"></script>
		<script src ="/resource/js/jquery/jquery.mobile-1.1.0.min.js"></script>
		<script src ="/resource/js/T3B.js"></script>
		<link rel="stylesheet" href="/resource/css/T3B.css" />
		<script type="text/javascript">
		var currentStart = 0;
		$(document).ready(function(){
			loadRoomList(0);
			$('select[name=find_room_by_type]').change(function(){currentStart=0;loadRoomList(0);});
			$('span.reload').click(function(){currentStart=0;loadRoomList(0);});
			$('#makeroombutton').click(function(){makeRoom($(this).parent().parent()); return false;});
			$('#roomlist > a').click(function(){currentStart+=15;loadRoomList(currentStart);});
			$('#topbtm a').eq(0).click(function(){$(document).scrollTop(0);}).end().eq(1).click(function(){$(document).scrollTop($('#roomlist').height()-40);});
			$('#head ul li a').eq(0).click(function(){viewRoomListMenu('roomlist');}).end()
				.eq(1).click(function(){viewRoomListMenu('status');}).end()
				.eq(2).click(function(){doLogout();});
			$('#make_icon').click(function(){viewRoomListMenu('makeroom');});
		});
		</script>
	</head>
	
	<body id="body">
	<div data-role="page" class="type-interior">
		<!-- /header -->
		<div data-role="header" data-position="fixed" data-theme="a" id="head">
			<div data-role="navbar" data-iconpos="right">
				<ul class="ui-body ui-body-b">
					<li><a data-theme="a" data-icon="search" class="ui-btn-active">Rooms</a></li>
					<li><a data-theme="a" data-icon="info">Status</a></li>
					<li><a data-theme="a" data-icon="delete">Logout</a></li>
				</ul>
			</div>
		</div><!-- /header -->
		
		<!-- /content -->
		<div id="content" data-role="content" data-theme="a"> 
			<!-- /roomlist -->
			<div id="roomlist">

				<div class="ui-grid-a">
					<!-- /select game type to search room-->
					<div id="find_room_by_type" class="ui-block-a">
						<select name="find_room_by_type" data-native-menu="false">
							<option value="0" selected="selected">빙고</option>
							<option value="1">주사위</option>
							<option value="2">사다리</option>
							<option value="3">해적</option>
						</select>
					</div>
					<!-- /select game type to search room-->

					<!-- /make room button-->
					<div id="make_icon" class="ui-block-b">
						<span data-role="button" data-inline="true" data-theme="a" data-icon="plus">
						방 생성
						</span>
					</div>
					<!-- /make room button-->
				</div>
				
				
				<div class="ui-grid-a">
					<!-- /search room button-->
					<div id="search_rooms" class="ui-block-a">
						<input type="search" name="search" />
					</div>
					<!-- /search rooms button -->

					<!-- /refresh room button -->
					<div id="refresh_icon" class="ui-block-b">
						<span class="reload" data-role="button" data-inline="true" data-theme="a" data-icon="refresh">
						방 갱신
						</span>
					</div>
					<!-- /refresh room button -->
				</div>
				
				
				<!-- /more rooms button -->
				<div class="roomlistWarp" data-role="collapsible-set" data-theme="a" data-content-theme="e">
					<div id="RoomList">
					</div>
				</div>
				<a data-role="button" data-icon="arrow-d">더보기</a>
				<!-- /more rooms button -->

			</div>
			<!-- / room list -->
			
			<!-- /status -->
			<div id="status">
				
				<!-- /user basic information -->
				<div class="info">
					<p>사용자 ID : <span id="statusUserID"></span></p>
					<p>별칭 : <span id="statusNickname"></span></p>
					<!--p>생년월일 : <span>2012</span>.<span>12</span>.<span>31</span></p-->
					<p>전/승/패 : <span id="statusTotal"></span>/<span id="statusWin"></span>/<span id="statusLose"></span></p>
				</div>
				<!-- /user basic information -->
				
				<!-- /game static information -->
				<div class="table" data-role="collapsible" data-collapsed="true" data-theme="a" data-content-theme="a">
					<h3>게임 통계</h3>
					<p>
						<table>
							<tr>
								<th>게임 명</th>
								<th>게임 횟수</th>
								<th>승리 횟수</th>
								<th>패배 횟수</th>
							</tr>
							<tr>
								<td>주사위</td>
								<td id="diceTotal"> 미 구현 </td>
								<td id="diceWin"> 미 구현 </td>
								<td id="diceLose"> 미 구현 </td>
							</tr>
							<tr>
								<td>빙고</td>
								<td id="bingoTotal">0</td>
								<td id="bingoWin">0</td>
								<td id="bingoLose">0</td>
							</tr>
							<tr>
								<td>사다리</td>
								<td id="ladderTotal"> 미 구현 </td>
								<td id="ladderin"> 미 구현 </td>
								<td id="ladderLose"> 미 구현 </td>
							</tr>
							<tr>
								<td>해적</td>
								<td id="pirateTotal"> 미 구현 </td>
								<td id="pirateWin"> 미 구현 </td>
								<td id="pirateLose"> 미 구현 </td>
							</tr>
							
						</table>
					</p>
				</div>
				<!-- /game static information -->
				<div id="withdraw"data-role="button" data-inline="true" data-theme="a" data-icon="refresh" style="margin-left:10px; padding:0px;">
				탈퇴
				</div>
				
			</div>
			<!-- /status -->

			<!-- /make room -->
			<div id="makeroom">
				<input type="hidden" name="gametype" value="0" />
				<div class="title">
					<div>게임 방 새로 만들기</div>
				</div>
				<div class="width80">
					<label for="roomname">방 이름</label>
					<input type="text" id="roomname" name="name" value="" maxlength="50" data-mini="true" />
				</div>
				<div>
					<label for="maxuser">참가자</label>
					<input type="range" name="maxuser" id="maxuser" value="2" min="2" max="8" data-theme="a" data-track-theme="b"/>
				</div>
				<div class="fl">
					<label for="private">공개 / 비 공개</label>
					<select name="private" id="private" data-role="slider" data-theme="a">
						<option value="0">공개</option>
						<option value="1">비공개</option>
					</select> 
				</div>
				<div class="fl ml40">
					<label for="roomtype">방종류</label>
					<select name="roomtype" id="roomtype" data-role="slider" data-theme="a">
						<option value="0">1회성</option>
						<option value="1">일반</option>
					</select> 
				</div>
				<div class="width80 clear">
					<label for="password">비밀 번호</label>
					<input type="password" id="password" name="password" maxlength="50" data-mini="true" />
				</div>

				<div class="width80">
					<span>게임 옵션</span>
					<div>
						<select name="select_game_type" data-native-menu="false" >
							<option value="0" selected="selected">빙고</option>
							<option value="1">주사위</option>
							<option value="2">사다리</option>
							<option value="3">해적</option>
						</select>
					</div>
					<div id="game_0">
						<label for="gameoption_0">빙고 줄</label>
						<input type="range" name="gameoption_0" id="gameoption_0" value="1" min="1" max="5" data-theme="a" data-track-theme="b"/>
					</div>
					<div id="game_1" class="dspn">
						<div>주사위값이 더 큰 사람이</div>
						<select id="gameoption_1" data-role="slider" data-theme="a">
							<option value="0">패자</option>
							<option value="1">승자</option>
						</select> 
					</div>
					<div id="game_2" class="dspn">
						방 생성후 조건 입력 
					</div>
					<div id="game_3" class="dspn">
						<div>칼을 꽂는 사람이</div>
						<select id="gameoption_3" data-role="slider" data-theme="a">
							<option value="0">패자</option>
							<option value="1">승자</option>
						</select> 
					</div>
				</div>
				<div id="makeroombutton" type="button" data-theme="a" data-icon="plus">
					만들기
				</div>

			</div><!-- /make room -->
			
		</div><!-- /content -->
		
		<!-- /footer -->	
		<div data-role="footer" data-position="fixed" data-theme="a" id="footer">
			<div id="topbtm">
				<a type="button" data-icon="arrow-u" data-iconpos="notext">Top</a>
				<a type="button" data-icon="arrow-d" data-iconpos="notext">Bottom</a>
			</div>
		</div><!-- /footer -->
		
	</div><!-- /page -->
	</body>
	
</html>

		
