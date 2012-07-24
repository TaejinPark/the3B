<?php
$opt = $room->getGameOption();
?>
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
			var room_seq = "<?php echo $room->getRoomSeq();?>";
			var userid = "<?php echo $member->getUserID();?>";
			var nickname = "<?php echo $member->getNickname();?>";
			var owner = "<?php echo $room->getOwner();?>";
			var sid = "<?php echo $member->getSessionID();?>";
			$(document).ready(function(){
				init();
				$('#debug input').keypress(function(e){
					if(e.keyCode==13) sendDebug();
				});
				$("#participant_list").css('display','none');
				$("#chat").css('display','block');
			});
			window.onbeforeunload=function(){
				$("#exit_button a").click();
			};
		</script>
	</head>
	
	<body id="body">
		
	<div data-role="page" class="type-interior">
	
		<!-- /header -->
		<div id="header" data-role="header" data-position="fixed" data-theme="a">
			<h3 data-inline="true"><?php echo $room->getName(); ?></h3>
			<div id="unfold">
				<a onclick="view_config('room_info'); view_folding('fold');"data-role="button" data-icon="arrow-d" data-iconpos="notext" data-theme="a">unfold</a>
				</div>
			<div id="fold">
				<a onclick="view_config('none'); view_folding('unfold');" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-theme="a">fold</a>
			</div>
		</div>
		<!-- /header -->
		
		<!-- /content -->
		<div id="content" data-role="content" data-theme="a">

			<!-- /room inform-->
			<div id="room_info">
				<div>참가자 : 
					<span id="joinUsers"><?php echo $room->getCurrentUser(); ?></span> / 
					<span id="maxUser"><?php echo $room->getMaxUser(); ?></span>
				</div>
				<div>게임 종류 : 
					<span>
						<?php
							$gametype = $room->getGameType();
							switch($gametype){
								case 0: echo "빙고"; break;
								case 1: echo "주사위"; break;
								case 2: echo "사다리"; break;
								case 3: echo "해적"; break;
							}
						?>
					</span>
				</div>
				<div>게임 옵션 : 
					<span id="gameOption">
						<?php 
							$opt = $room->getGameOption();
							switch($room->getGameType()){
								case 0: // 빙고
									echo $opt[0] , " 줄 완성시 승리"; break;
								case 1: // 주사위
									echo "주사위 합이 큰 사람이"; 
									if($opt[0]) echo "승리"; 
									else echo "패배" ;
									break;
								case 2: // 사다리
									echo "사다리"; break;
								case 3: // 해적
									echo "당첨 칼을 꽂는 사람이"; 
									if($opt[0]) echo "승리"; 
									else echo "패배" ;	
									break;
							}
						?>
				</div>
				<a id="config_change" onclick="view_config('room_config');" type="button" data-inline="true;">설정 변경</a>
			</div>
			<!-- /room inform -->

			<!-- /room config-->
			<div id="room_config">
				<div>
					<div id="participant_num" data-role="fieldcontain">
						<div>참가자</div>
					 	<input type="range" name="maxuser" value="<?php echo $room->getMaxUser(); ?>" min="2" max="8" data-theme="e"/>
					</div>
					<div id="select_game_type">
						<div>게임 종류</div>
						<select id="select_game_type" name="select_game_type" data-native-menu="false" onchange="viewGameOption(value)">
							<option value="bingo">빙고</option>
							<option value="dice">주사위</option>
							<option value="ladder">사다리</option>
							<option value="pirate">해적</option>
						</select>
					</div>
					<input type="hidden" name="select_game_type" value="bingo" />
					<div id="bingo_option_line"data-role="fieldcontain">
						<div>빙고 라인 갯수</div>
					 	<input type="range" name="gameoption" value="<?php echo $opt[0]; ?>" min="2" max="5" data-theme="e"/>
					</div>

					<div >
						<span>게임 옵션</span>
						<div>
							<select name="select_game_type" data-native-menu="false" onchange="viewGameOption(value)">
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
							<span>주사위값이 더 큰 사람이</span>
							<select id="gameoption_1" name="gameoption_1" data-role="slider" data-theme="a">
								<option value="0">패자</option>
								<option value="1">승자</option>
							</select>
						</div>
						<div id="game_2" class="dspn" name="gameoption_2">
							방 생성후 조건 입력 
						</div>
						<div id="game_3" class="dspn">
							<span>당첨 칼을 꽂는 사람이</span>
							<select id="gameoption_3" name="gameoption_3" data-role="slider" data-theme="a">
								<option value="0">패자</option>
								<option value="1">승자</option>
							</select> 
						</div>
					</div>

					<a id="config_confirm" type="button" data-inline="true;">적용</a>
				</div>
			</div>
			<!-- /room config -->

			<div id="chat" style="display: none;"></div>
			<div id="participant_list">
				<p>참가자</p>
			</div>
			<div id="debug">
				<div></div>
				<input type="text" />
			</div>

			<!-- /game display-->
			<div id="gamedisplay">
				<!-- /game message-->
				<div id="messageWindow"></div>
				<div id="turn">
					<div class="turnuser">현재 순서 아이디</div>
					<div><a data-role="button" data-icon="arrow-r" data-iconpos="notext" data-theme="a" data-inline="true">unfold</a></div>
					<div class="turnuser textalignright"> 다음 순서 아이디</div>
				</div>
				<!-- /game message-->

				<!-- /dice game-->
				<div id="dice">
					<center>
						<canvas id="dice_canvas">
							this browser is not support canvas element.<br>
							이 브라우저는 캔바스를 지원하지 않습니다.
						</canvas>

					</center>
					<div id="cast_dice">
						<a data-role="button" data-theme="b" onclick="draw_dice()">던지기</a>
					</div>
					<div id="dice_result">
						<a data-role='button' data-theme='b'>결과 확인</a>
					</div>
				</div>
				<!-- /dice game-->

				<!-- pirate game -->
					<div id="pirate">
						<canvas>
							this browser is not support canvas element.<br>
							이 브라우저는 캔바스를 지원하지 않습니다.
						</canvas>
					</div>
				<!-- pirate game -->

				<!-- /ladder game-->
					사다리 타기
					<canvas>
						this browser is not support canvas element.<br>
						이 브라우저는 캔바스를 지원하지 않습니다.
					</canvas>
				<!-- /ladder game-->

				<!-- /bingo game-->
				<div id="bingo">
					
					<table id="bingoTable">
						<tr>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
						</tr>
						<tr>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
						</tr>
						<tr>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
						</tr>
						<tr>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td><!-- 중복시 색 -->
						</tr>
						<tr>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td><!-- 미 선택시 색 -->
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
							<td><a data-role="button" data-mini="true" data-inline="true" data-theme="b">x</a></td>
						</tr>
					</table>
					<div id="remaintime">남은 시간 : <span>25</span> 초 </div>
					<div>	
						<div>미 선택 <div id="bingoUnselect"></div> </div><!-- 숫자 선택을 모두 하지 않고 완료시 경우 미 선택 숫자 표시-->
						<br>
						<label for="slider" class="ui-input-text ui-slider">수동 숫자 입력 (번호를 선택하고 입력할 위치를 선택하세요)</label>
				 		<input type="range" name="slider" id="currentSelect" value="1" min="1" max="25" data-theme="e"/><!-- 게임 시작 초기 설정 -->
						<a data-role="button" data-mini="true" data-inline="true" data-theme="b" id="inputEnd">작성 완료</a><!-- 게임 시작 초기 설정 -->
					</div>
					<div id="okSelect">
						<a data-role="button" data-mini="true" data-inline="true" data-theme="b">선택 완료</a><!-- 게임 중일 경우 -->
					</div>
				</div>
				<!-- /bingo game -->
			</div>
			<!-- /game display-->
			<div id="gameResult">
			</div>

		</div>
		<!-- /content -->
		
		<!-- /footer -->	
		<div id="footer" data-role="footer" data-position="fixed" data-theme="a">
			<div id="chat_input_form">
				<div id="chat_input"><input type="text" id="msg"></div>
				<div id="chat_send" ><a type="button">전송</a></div>
			</div>
			<div id="divider"></div>
			<div>
				<div id="chat_list_button"><a type="button" onclick="viewChat();">채팅창</a></div>
				<div id="participant_button"><a type="button" onclick="viewParticipant();">참가자</a></div>
				<div id="exit_button">
					<a type="button">종료</a>
				</div>
				<div id="play_button">
					<?php echo ($member->getUserID()==$room->getOwner()?'<a type="button" id="start_button">시작</a>':'<a type="button" id="ready_button">준비</a>');?>	
					<a type="button" id="already_button">준비취소</a>
				</div>
			</div>
		</div>
		<!-- /footer -->
		
	</div><!-- /page -->
	</body>
	
</html>

		
