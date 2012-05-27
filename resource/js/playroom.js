$(window).resize(function() {
	resizeContent();
});

$("div[data-role='page']").live( "pageshow", function( event )
{
	resizeContent();
});

var play = false;

function resizeContent()
{
	var contentHeight ;
	var browserWidth = document.documentElement.clientWidth;
	var browserHeight = document.documentElement.clientHeight;
	var headerHeight = parseInt( $("div[data-role='header']").css( "height" ) );
	var footerHeight = parseInt( $("div[data-role='footer']").height());
	
	if(navigator.userAgent.indexOf('iPhone') != -1 || navigator.userAgent.indexOf('iphone') != -1){
		$("#content").css("height" , browserHeight - headerHeight - footerHeight +60);
	}
	else
		$("#content").css("height" , browserHeight - headerHeight - footerHeight);
	contentHeight = $("#content").height();
	$("#chat").css("height" , contentHeight);
	$("#gamedisplay").css("height" , browserHeight);
	
	if(	$("#participant_list").height() < contentHeight)
		$("#participant_list").css("height" , contentHeight);
	
	$("#chat").css("width" , browserWidth -30);
	$("#button_list").css("width" , browserWidth-10);
	$("#chat_input").css("width" , browserWidth - $("#chat_send").width() - 30);
	
	$(".turnuser").css("width", browserWidth / 2 - 24);
}

function view_config(id){
	$("#room_info").css("display" , "none");
	$("#room_config").css("display" , "none");
	if(id != 'none')
		$("#"+id).css("display" , "block");
}

function view_folding(flag){
	$("#unfold").css("display" , "none");
	$("#fold").css("display" , "none");
	$("#"+flag).css("display" , "block");
	if(flag == 'unfold'){
		$("#room_info").css("display" , "none");
		$("#room_config").css("display" , "none");
	}
}

function viewPlay(){
	$("#chat").css('display','none');
	$("#participant_list").css('display','none');
	$("#gamedisplay").css('display','block');
}

function viewChat(){
	if($("#chat").css('display')=='block') return viewPlay();
	$("#participant_list").css('display','none');
	if(play) {
		$("#gamedisplay").css('display','none');
	}
	$("#chat").css('display','block');
}

function viewParticipant(){
	if($("#participant_list").css('display')=='block') return viewChat();
	$("#chat").css('display','none');
	if(play) {
		$("#gamedisplay").css('display','none');
	}
	$("#participant_list").css('display','block');
}

/* websocket */

var socket;
var sendCmd;
var userlist;
var debug = true;
var currentNumber = 1;
var bingoSelect = [];
var selectActivate = false;
var currentSelectTime = 0;
var bingoUser = [];
var bingoEndUser = [];
var bingoLine = 0;
var currentBingo = 0;
var interval = null;
var currentNickname = '';

function init(){
  var host = "ws://115.68.23.155:4279/";
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);
    socket.onopen    = connected;
    socket.onmessage = function(msg){ log("Received: "+msg.data); process(msg.data); };
    socket.onclose   = function(msg){ log("Disconnected - status "+this.readyState); };
  }
  catch(ex){ log(ex); }
}

function sendDebug(){
  var txt,msg;
  txt = $("#debug input");
  msg = txt.val();
  if(!msg){ alert("Message can not be empty"); return; }
  txt.val("");
  txt.focus();
  var data = msg.indexOf(' ');
  var cmd;
  if(data==-1) { data = ''; cmd = msg; }
  else { data = eval('('+msg.substr(data+1)+')'); cmd = msg.substr(0,msg.indexOf(' ')); }
  data = {cmd:cmd,data:data};
  try{ socket.send(JSON.stringify(data)); log('Sent: '+JSON.stringify(data)); } catch(ex){ log(ex); }
}

// Utilities
function log(msg){ if(debug) $("#debug div").append("<br>"+msg); }
function trim(str) { return str.replace(/^\s\s*/, '').replace(/\s\s*$/, ''); }

String.prototype.trim = function() { return this.replace(/^\s\s*/, '').replace(/\s\s*$/, ''); }

function connected(msg){
	log("Welcome - status "+this.readyState);
	sendLoginInfo(sid,userid);
}

function process(msg){
	if(msg.substr(0,1)!="{") msg = msg.substr(1);
	if(msg.substr(msg.length-1,1)!="}") msg = msg.substr(0,msg.length-1);
	try{ 
	var data = JSON.parse(msg);
	} catch (ex){ log(ex); }
	switch(data.cmd){
		case "JOIN": chatAppend(data.data.Nickname+"님이 참가 하셨습니다."); userAppend(data.data.UserID,data.data.Nickname); break;
		case "USERLIST": makeUserList(data.data); break;
		case "CHAT": chatAppend(data.data.Nickname+": "+data.data.Message); break;
		case "KICK": chatAppend($('.user_'+data.data.UserID+' span').text()+"님이 강퇴 강하셨습니다.");
					 $('.user_'+data.data.UserID).remove(); break;
		case "CHANGE_SETTING": chatAppend("방 설정이 다음과 같이 변경되었습니다.");
							   chatAppend("최대 인원: "+data.data.MaxUser+"명, 승리조건: "+data.data.GameOption+"줄");
							   $("#maxUsers").text(data.data.MaxUser); $("#gameOption").text(data.data.GameOption);
							   $("#room_config").find("input").filter("[name=maxuser]").val(data.data.MaxUser).end()
							   .filter("[name=gameoption]").val(data.data.GameOption);
							   break;
		case "CHANGE_OWNER": owner = data.data.UserID; chatAppend("방장이 변경되었습니다. 방장:"+data.data.Nickname);
							 sendCmd="USERLIST"; send("USERLIST",{});
							 break;
		case "READY": chatAppend(data.data.Nickname+"님이 준비가 완료되었습니다."); break;
		case "UNREADY": chatAppend(data.data.Nickname+"님이 준비를 취소 하였습니다."); break;
		case "START": chatAppend("게임이 곧 시작됩니다. 준비하세요!"); setTimeout(startBingo,4000); break;
		case "QUIT": if(nickname==data.data.Nickname) location.href="/roomlist/";
					 chatAppend($('.user_'+data.data.UserID+' span').text()+"님이 방에서 나갔습니다.");
					 $('.nick_'+data.data.Nickname).parent().remove(); break;
		case "BINGO_START": $("#remaintime").css('display','none').next().css('display','none'); $("#turn").css('display','block');
							$("#bingoTable a").unbind("click").click(bingo);
							break;
		case "BINGO_CURRENT": $("#turn > div").eq(0).text(data.data.CurrentNickname).end().eq(1).text(data.data.NextNickname);
							  currentNickname = data.data.CurrentNickname;
							  if(data.data.CurrentNickname==nickname) showMyTurn();
							  break;
		case "BINGO_SELECT": bingoUser = []; bingoEndUser = []; markSelect(data.data); break;
		case "BINGO_BINGO": bingoUser.push(data.data.Nickname);
							message(bingoUser.join(", ")+"님이 빙고 한줄을 완성 했습니다!"+(bingoEndUser.length>0?"<br />"+bingoEndUser.join(", ")+"님이 빙고를 완성 했습니다!":""));
							break;
		case "BINGO_LAST": bingoUser.push(data.data.Nickname);
						   message((bingoUser.length>0?bingoUser.join(", ")+"님이 빙고 한줄을 완성 했습니다!":"")+bingoEndUser.join(", ")+"님이 빙고를 완성 했습니다!");
						   break;
		case "BINGO_END": showResult(data.data.result); break;
		case "INSTANCE_EXIT": setTimeout(goExit,10000); break;
		case "OK":
			switch(sendCmd){
				case "LOGIN": sendJoin(); break;
				case "JOIN": initJoin(); chatAppend("방에 접속하였습니다."); sendUserList(); break;
				case "READY": $("#play_button a").text("준비취소"); break;
				case "UNREADY": $("#play_button a").text("준비"); break;
			}
			break;
	}
}

function chatAppend(msg){
	var obj = $("#chat");
	obj.append("<br />"+msg);
}

function message(msg){
	$("#messageWindow").html(msg);
}

function userAppend(a_userid,a_nickname){
	var str = '<div class="user_'+a_userid+'">'+
			'<a class="nick_'+a_nickname+'" type="button" data-inline="true">'+(owner==a_userid?'방장':'강퇴')+'</a>'+
			'<span>'+a_nickname+'</span>'+
			'</div>';
	$("#participant_list").append(str).parent().trigger("create");
	$('#participant_list div a').unbind('click').click(function(){
		if($(this).text()=="방장") return;
		if(owner!=userid) return;
		var kickuser = $(this).parent().attr('class').replace("user_","");
		if(kickuser == userid) { alert("자기 자신은 강퇴 안되요 ^^"); return; }
		sendCmd = "KICK";
		var data = {};
		data.UserID = kickuser;
		send("KICK",data);
	});
}

function send(command,data){
	data = {cmd:command,data:data};
	try{
		socket.send(JSON.stringify(data));
		log('Sent: '+JSON.stringify(data));
	} catch(ex){ log(ex); }
}

function sendLoginInfo(sessionid,userid){
	sendCmd = "LOGIN";
	var data = {};
	data.Sessionid = sessionid;
	data.UserID = userid;
	send("LOGIN",data);
}

function sendJoin(){
	sendCmd = "JOIN";
	var data = {};
	data.room_seq = room_seq;
	send("JOIN",data);
}

function sendUserList(){
	sendCmd = "USERLIST";
	var data = {};
	send("USERLIST",data);
}

function makeUserList(list){
	$("#participant_list").html("");
	for(var a=0; a<list.length; a++){
		userAppend(list[a].UserID,list[a].Nickname);
	}
	$("#joinUsers").text(list.length);
}

function sendChat(){
	sendCmd = "CHAT";
	var data = {};
	data.Message = $("#msg").val();
	$("#msg").val("");
	send("CHAT",data);
}

function sendReady(){
	sendCmd = "READY";
	var data = {};
	send("READY",data);
}

function sendUnready(){
	sendCmd = "UNREADY";
	var data = {};
	send("UNREADY",data);
}

function sendStart(){
	sendCmd = "START";
	var data = {};
	send("START",data);
}

function initJoin(){
	$("#msg").keypress(function(e){
		if(e.keyCode==13) sendChat();
	});
	$("#play_button a").click(function(){
		if($(this).text()=="시작") sendStart();
		else if($(this).text()=="준비") sendReady();
		else if($(this).text()=="준비취소") sendUnready();
	});
	$("#exit_button a").click(function(){
		sendCmd = "QUIT";
		send("QUIT",{});
	});
	$("#config_confirm").click(function(){
		if(owner!=userid) return;
		var data = {};
		var obj = $(this).parent().find("input");
		data.MaxUser = obj.filter('[name=maxuser]').val();
		data.GameOption = obj.filter('[name=gameoption]').val();
		sendCmd = "CHANGE_SETTING";
		send("CHANGE_SETTING",data);
		$("#fold a").click();
	});
}

function startBingo(){
	play = true;
	viewPlay();
	$("#bingoTable a").click(insertBingo);
	$("#currentSelect").change(function(){
		currentNumber = $(this).val();
		if(!selectActivate) {
			viewUnselect();
			selectActivate = true;
		}
	});
	$("#inputEnd").click(function(){
		sendCmd = "BINGO_WRITED";
		send("BINGO_WRITED",{});
		interval = setInterval(forceStart,(50-currentSelectTime)*1000);
		currentSelectTime = 49;
		$("#inputEnd").parent().css('display','none');
	});
	$("#okSelect a").click(currentSelectEnd);

	//init
	bingoLine = $("#bingo_option_line input").val();
	currentBingo = 0;
	bingoUser = [];
	bingoEndUser = [];
	currentSelectTime = 0;
	selectActivate = false;
	bingoSelect = [];
	currentNumber = 1;
	interval = null;

	setTimeout(selectEnd,1000);
}

function insertBingo(){
	if(currentSelectTime==50) return;
	//font-size 13px;
	if($(this).children("span").children("span").text() == "x"){
		bingoSelect.push(currentNumber);
		$(this).children("span").children("span").text(currentNumber++);
		$(this).attr('data-theme','c');
		if(currentNumber<26) $("#currentSelect").val(currentNumber);
		if(selectActivate || currentNumber==26) viewUnselect();
	} else {
		var arr = {};
		var idx = 1;
		var obj = $("#bingoTable a");
		obj.each(function(){
			if($(this).text()=="x") return;
			if(typeof arr[$(this).text()] == "undefined") {
				arr[$(this).text()] = [];
				arr[$(this).text()].push(idx++);
			} else {
				arr[$(this).text()].push(idx++);
				for(var a=0,loopa=arr[$(this).text()].length; a<loopa; a++){
					obj.eq(arr[$(this).text()][a]).attr('data-theme','e');
				}
			}
		});
	}
	$("#bingoTable a").trigger("create");
	$("#bingoTable a").css('font-size','13px');
	$(this).css('font-size',"15px");
}

function viewUnselect(){
	var unselectList = [];
	for(var a=1; a<=25; a++){
		var flag = false;
		for(var b=0, loopb= bingoSelect.length; b<loopb; b++){
			if(a==bingoSelect[b]) {
				flag = true;
				break;
			}
		}
		if(!flag) unselectList.push(a);
	}
	$("#bingoUnselect").text(unselectList.join(", "));
}

function selectEnd(){
	$("#remaintime span").text(50 - ++currentSelectTime);
	if(currentSelectTime<50) setTimeout(selectEnd,1000);
	else {
		forceInsert();
		if(interval==null)
			forceStart();
	}
}

function forceInsert(){
	//insert
	$(this).attr('data-theme','c');
}

function forceStart(){
	if(owner != userid) return;
	sendCmd = "BINGO_START";
	send("BINGO_START",{});
	clearInterval(interval);
}

function bingo(){
	if(currentNickname!=nickname) return;
	if($(this).attr('data-theme')!='c') return;
	$("#bingoTable a[data-theme=e]").attr('data-theme','c');
	$(this).attr('data-theme','e');
	$("#bingoTable").trigger("create");
	$("#bingoTable a").css('font-size','13px');
	$(this).css('font-size',"15px");
}

function currentSelectEnd(){
	var currentNumber = $("#bingoTable a[data-theme=e]").text();
	var data = {};
	$("#remaintime").css('display','none');
	$("#okSelect").css('display','none');
	currentSelectTime = 14;
	data.SelectedNumber = currentNumber;
	send("BINGO_SELECT",data);
}

function showMyTurn(){
	$("#okSelect").css('display','block');
	currentSelectTime = 0;
	$("#remaintime").css('display','block');
	$("#remaintime span").text(15);
	setTimeout(showTurnRemainTime,1000);
}

function showTurnRemainTime(){
	$("#remaintime span").text(15 - ++currentSelectTime);
	if(currentSelectTime<15) setTimeout(showTurnRemainTime,1000);
	else {
		//select Random
		currentSelectEnd();
	}
}

function markSelect(data){
	if(currentBingo>=bingoLine) return;
	var obj = $("#bingoTable a");
	obj.each(function(idx){
		if(parseInt($(this).text())==data.SelectedNumber){
			$(this).buttonMarkup({ theme: "a" });
			var curidx = idx + 1;
			var col = curidx % 5;
			var row = parseInt(curidx / 5);
			var addBingo = 0;
			//row check
			if(obj.slice(row*5-1,row*5+4).filter("[data-theme=a]").size()==5) addBingo++;
			//col check
			var tmp = 0;
			for(var a=0; a<5; a++)
				if(obj.eq(col-1+a*5).attr("data-theme")=="a")
					tmp++;
			if(tmp==5) addBingo++;
			//cross check
			if(col == row){
				tmp = 0;
				for(var a=0; a<5; a++)
					if(obj.eq(a+a*5).attr("data-theme")=="a")
						tmp++;
				if(tmp==5) addBingo++;
			}
			if(5-col == row){
				tmp = 0;
				for(var a=0; a<5; a++)
					if(obj.eq(4-a+a*5).attr("data-theme")=="a")
						tmp++;
				if(tmp==5) addBingo++;
			}
			for(var a=0; a<addBingo; a++){
				sendCmd = "BINGO_BINGO";
				send("BINGO_BINGO",{});
			}
			currentBingo += addBingo;
			if(currentBingo>=bingoLine){
				sendCmd = "BINGO_LAST";
				send("BINGO_LAST",{});
				$("#bingoTable a").unbind("click");
			}
		}
	});
	$("#bingoTable").trigger("create");
}

function showResult(list){
	var str = "";
	for(var a=0,loopa=list.length; a<loopa; a++){
		str += '<div>'+
				'<span>'+list[a].Nickname+'</span>'+
				list[a].result+
				'</div>';
	}
	$("#gamedisplay").css('display','none');
	$("#gameResult").html(str).css('display','block');
}

function goExit(){
	sendCmd = "QUIT";
	send("QUIT",{});
	setTimeout(function(){
		location.href="/roomlist/";
	},1000);
}