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

function view(id){
	if(id == "participant_list"){
		if($("#participant_list").css("display") == "none"){
			$("#chat").css("display" , "none");
			if(play)
				$("#gamedisplay").css("display" , "none");
		}
		else{
			$("#chat").css("display" , "block");
			if(play)
				$("#gamedisplay").css("display" , "block");
		}
	}
			
	if($("#"+id).css("display") == "block"){
		$("#"+id).css("display" , "none");
	}
	else
		$("#"+id).css("display" , "block");
	
	if(id == "chat" && $("#gamedisplay").css("display")=="none"){
		$("#gamedisplay").css("display","block");
		$("#participant_list").css("display","none");
	}
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

/* websocket */

var socket;
var sendCmd;
var userlist;
var debug = true;

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
		case "KICK": chatAppend($('.user_'+data.data.UserID+' span').text()+"님이 강퇴 강하셨습니다."); $('.user_'+data.data.UserID).remove(); break;
		case "CHANGE_SETTING": chatAppend("방 설정이 다음과 같이 변경되었습니다."); chatAppend("최대 인원: "+data.data.MaxUser+"명, 승리조건: "+data.data.GameOption+"줄");
							   $("#maxUsers").text(data.data.MaxUser); $("#gameOption").text(data.data.GameOption);
							   $("#room_config").find("input").filter("[name=maxuser]").val(data.data.MaxUser).end().filter("[name=gameoption]").val(data.data.GameOption);
							   break;
		case "QUIT": if(nickname==data.data.Nickname) location.href="/roomlist/"; $('.user_'+data.data.Nickname).parent().remove(); break;
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

function userAppend(userid,nickname){
	var str = '<div class="user_'+userid+'">'+
			'<a class="user_'+nickname+'" type="button" data-inline="true">'+(owner==userid?'방장':'강퇴')+'</a>'+
			'<span>'+nickname+'</span>'+
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
		data.GameOption = obj.filter('[name=s\gameoption]').val();
		sendCmd = "CHANGE_SETTING";
		send("CHANGE_SETTING",data);
	});
}