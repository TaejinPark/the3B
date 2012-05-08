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
	</script>
	</head>
	
	<body id="body">
		
	<div data-role="page" class="type-interior">
	
		<!-- /header -->
		<div data-role="header" data-position="fixed" data-theme="b" >
			<div data-role="navbar" data-iconpos="right">
				<ul class="ui-body ui-body-b">
				    <li><a data-theme="c" data-icon="search" class="ui-btn-active" onclick="view_room_list('roomlist')">Rooms</a></li>
				    <li><a data-theme="c" data-icon="info" onclick="view_room_list('status')">Status</a></li>
					<li><a data-theme="c" data-icon="delete">Logout</a></li>
				</ul>
				
			</div>
		</div><!-- /header -->
		
		<!-- /content -->
		<div id="content" data-role="content" style="padding:0px" width="100%"> 
		
			<div id="roomlist" style="display:block; position:absolute;">
				<div class="ui-grid-a" style="border:0px; padding:0px; margin:3px;">	
					<div id="select_game_type" class="ui-block-a" style="margin-right:3px;">
						<select name="select_name_type" data-native-menu="false">
							<option value="bingo">빙고</option>
							<option value="dice">주사위</option>
							<option value="ladder">사다리</option>
							<option value="pirate">해적</option>
						</select>
					</div>
					<div id="make_icon" class="ui-block-b" align="right" onclick="view_room_list('makeroom')">
						<span href="" data-role="button" data-inline="true" data-theme="a" data-icon="plus" style="padding:0px;margin:0px;">
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
						<span data-role="button" data-inline="true" data-theme="a" data-icon="refresh" style="padding:0px;margin:0px;">
						방 갱신
						</span>
					</div>
				</div>
				
				<!--  -->
				
				<div data-role="collapsible-set" data-theme="b" data-content-theme="e" style="padding:0px 9px; margin:0px;">
			
					<div data-role="collapsible" data-collapsed="true" data-content-theme="e">
					    <h3>
					    	<span>Room1</span>
					    	<img src="/resource/img/lock_icon.png" width="24px" height="24px" style="float:right; margin:0px 5px;">
					    	<span style="float:right; margin:0px 5px;"> 
					    			[<span>3</span>/
					    			<span>8</span>]
					    	</span>
					    	<span style="float:right; margin:0px 5px;">빙고</span>
					    </h3>
					    <p>
								<div>참가자 : 
					    			<span>3</span>/
					    			<span>8</span>
					    		</div>
						    	<div>게임 종류 : 
					    			<span>빙고</span>
					    		</div>
					    		<div>-Option-<br>
					    			승리 빙고 : 
					    			<span>1줄</span>
					    		</div>
					 	</p>
					</div>
					<div data-role="collapsible" data-collapsed="true" data-content-theme="e">
					    <h3>
					    	<span>Room2</span>
					    	<img src="/resource/img/unlock_icon.png" width="24px" height="24px" style="float:right; margin:0px 5px;">
					    	<span style="float:right; margin:0px 5px;"> 
					    			[<span>6</span>/
					    			<span>8</span>]
					    	</span>
					    	<span style="float:right; margin:0px 5px;">빙고</span>
					    </h3>
					    <p>
								<div>참가자 : 
					    			<span>6</span>/
					    			<span>8</span>
					    		</div>
						    	<div>게임 종류 : 
					    			<span>빙고</span>
					    		</div>
					    		<div>-Option-<br>
					    			승리 빙고 : 
					    			<span>3줄</span>
					    		</div>
					 	</p>
					</div>
					<div data-role="collapsible" data-collapsed="true" data-content-theme="e">
					    <h3>
					    	<span>Room1</span>
					    	<img src="/resource/img/lock_icon.png" width="24px" height="24px" style="float:right; margin:0px 5px;">
					    	<span style="float:right; margin:0px 5px;"> 
					    			[<span>3</span>/
					    			<span>8</span>]
					    	</span>
					    	<span style="float:right; margin:0px 5px;">빙고</span>
					    </h3>
					    <p>
								<div>참가자 : 
					    			<span>3</span>/
					    			<span>8</span>
					    		</div>
						    	<div>게임 종류 : 
					    			<span>빙고</span>
					    		</div>
					    		<div>-Option-<br>
					    			승리 빙고 : 
					    			<span>1줄</span>
					    		</div>
					 	</p>
					</div>
					<div data-role="collapsible" data-collapsed="true" data-content-theme="e">
					    <h3>
					    	<span>Room2</span>
					    	<img src="/resource/img/unlock_icon.png" width="24px" height="24px" style="float:right; margin:0px 5px;">
					    	<span style="float:right; margin:0px 5px;"> 
					    			[<span>6</span>/
					    			<span>8</span>]
					    	</span>
					    	<span style="float:right; margin:0px 5px;">빙고</span>
					    </h3>
					    <p>
								<div>참가자 : 
					    			<span>6</span>/
					    			<span>8</span>
					    		</div>
						    	<div>게임 종류 : 
					    			<span>빙고</span>
					    		</div>
					    		<div>-Option-<br>
					    			승리 빙고 : 
					    			<span>3줄</span>
					    		</div>
					 	</p>
					</div>
					
					<div style="height:40px;"></div>
				</div>
			</div><!-- / room -->
			
			<!-- /status -->
			<div id="status" style="position:abolute; display:none;">
				
				<div style="border:solid 1px black; margin:30px; padding:10px;">
					<p style="margin:5px;">사용자 ID : <span>the3B</span></p>
					<p style="margin:5px;">별칭 : bokbolbok</p>
					<p style="margin:5px;">생년월일 : <span>2012</span>.<span>12</span>.<span>31</span></p>
					<p style="margin:5px;">전/승/패 : <span>100</span>/<span>50</span>/<span>50</span></p>
				</div>
				
				<div data-role="collapsible" data-collapsed="true" data-theme="a" data-content-theme="a" style="margin:20px;">
					    <h3>
					    	<span>게임 통계</span>
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
									<td>10</td>
									<td>4</td>
									<td>6</td>
								</tr>
								<tr>
									<td>가위바위보</td>
									<td>10</td>
									<td>4</td>
									<td>6</td>
								</tr>
							</table>
					 	</p>
				</div>
				<div data-role="button" data-inline="true" data-theme="a" data-icon="refresh" style="margin-left:10px; padding:0px;">
				탈퇴
				</div>
				
			</div><!-- /status -->

			<!-- /make room -->
			<div id="makeroom" style="position:abolute; display:none;">
				<div style="margin:20px;">
					<center>게임 방 새로 만들기</center>
				</div>
				<div style="margin:20px; width:80%;">
					<label for="roomname">방 이름</label>
					<input type="text" id="roomname" name="roomname" value="" maxlength="50" data-mini="true" />
				</div>
				<div style="margin:20px;">
				    <label for="participant">참가자</label>
					<input type="range" name="slider" id="participant" value="2" min="2" max="8" data-theme="a" data-track-theme="b"/>
				</div>
				<div style="margin:20px; width:80%;">
					<label for="roompassword">비밀 번호</label>
					<input type="text" id="roompassword" name="roompassword" value="" maxlength="50" data-mini="true" />
				</div>
				<div style="margin:20px;">
					<label for="private">공개 / 비 공개</label>
					<select name="private" id="private" data-role="slider" data-theme="a">
						<option value="no">공개</option>
						<option value="yes">비공개</option>
					</select> 
					<div data-role="button" data-inline="true" data-theme="a" data-icon="plus" style="float:right;">
					만들기
					</div>
				</div>
			</div><!-- /make room -->
			
			
		</div><!-- /content -->
		
		<!-- /footer -->	
		<div data-role="footer" data-position="fixed" data-theme="b">
			<center class="padding">
					<span>Top</span>
					<span> | </span>
					<span>Bottom </span>
			</center>
		</div><!-- /footer -->
		
	</div><!-- /page -->
	</body>
	
</html>

        
