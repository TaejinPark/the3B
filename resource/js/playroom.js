$(window).resize(function() {
	resizeContent();
});

$("div[data-role='page']").live( "pageshow", function( event )
{
	resizeContent();
});

var play = false;
var contentHeight = 0 ;
function resizeContent()
{
	var header_obj = $("div[data-role='header']") ;
	var footer_obj = $("div[data-role='footer']") ;
	var browserWidth = document.documentElement.clientWidth;
	var browserHeight = document.documentElement.clientHeight;
    var headerHeight = parseInt( header_obj.height())+parseInt(header_obj.css("padding-bottom"))+parseInt(header_obj.css("padding-top"))+parseInt(header_obj.css("border-top-width"))+parseInt(header_obj.css("border-bottom-width"));
    var footerHeight = parseInt(footer_obj.height())+parseInt(footer_obj.css("padding-bottom"))+parseInt(footer_obj.css("padding-top"))+parseInt($("#footer").css("border-bottom-width"))+parseInt(footer_obj.css("border-top-width"));
    
    // set content size as browswer heigt - fixed header height
    if(navigator.userAgent.indexOf('iPhone') != -1 || navigator.userAgent.indexOf('iphone') != -1)
		$("#content").css("height" , browserHeight - headerHeight - footerHeight + 65); // if browser is iphone , content height becomes more higher than PC browser
	else // normal browser
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
	
	var ctx = document.getElementById("dice_canvas").getContext("2d");
	ctx.fillStyle = "white";
	ctx.font = "italic 20pt Calibri" ;
	ctx.fillText("Let's Dice!!!",80,80);
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
	if($("#chat").css('display')=='block' && play) return viewPlay();
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

function viewGameOption(value){
	$("#game_0").css("display","none");
	$("#game_1").css("display","none");
	$("#game_2").css("display","none");
	$("#game_3").css("display","none");
	$("#game_"+value).css("display","block");
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
var currentSelectTime2 = 0;
var bingoUser = [];
var bingoEndUser = [];
var bingoLine = 0;
var currentBingo = 0;
var interval = null;
var interval2 = null;
var currentNickname = '';

function init(){
  var host = "ws://115.68.23.155:4279/";
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);
    socket.onopen	= function(msg){ log("Welcome - status "+this.readyState);sendLoginInfo(sid,userid);}
    socket.onmessage= function(msg){ log("Received: "+msg.data); process(msg.data); };
    socket.onclose	= function(msg){ log("Disconnected - status "+this.readyState); };
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

function process(msg){
	if(msg.substr(0,1)!="{") msg = msg.substr(1);
	if(msg.substr(msg.length-1,1)!="}") msg = msg.substr(0,msg.length-1);
	try{ 
	var data = JSON.parse(msg);
	} catch (ex){ log(ex); }
	switch(data.cmd){
		case "JOIN": 
			chatAppend('['+data.data.Nickname+"] 님이 참가 하셨습니다."); 
			userAppend(data.data.UserID,data.data.Nickname); break;
		
		case "USERLIST": 
			makeUserList(data.data); break;
		
		case "CHAT": 
			chatAppend(data.data.Nickname+": "+data.data.Message);break;
		
		case "KICK": 
			chatAppend('['+data.data.Nickname+"] 님이 강퇴 당하셨습니다.");
			$('.user_'+data.data.UserID).remove(); break;
		
		case "CHANGE_SETTING": 
			chatAppend("방 설정이 다음과 같이 변경되었습니다.");
			chatAppend("최대 인원: "+data.data.MaxUser+"명, 승리조건: "+data.data.GameOption+"줄");
			$("#maxUsers").text(data.data.MaxUser); $("#gameOption").text(data.data.GameOption);
			$("#room_config").find("input").filter("[name=maxuser]").val(data.data.MaxUser).end()
			.filter("[name=gameoption]").val(data.data.GameOption);
			break;
		
		case "CHANGE_OWNER": 
			owner = data.data.UserID; chatAppend('['+data.data.Nickname+'] 님이 방장이 셨습니다.');
			sendCmd="USERLIST"; send("USERLIST",{});
			break;
		
		case "READY": 
			chatAppend('['+data.data.Nickname+"] 님이 준비가 완료되었습니다."); break;
		
		case "UNREADY": 
			chatAppend('['+data.data.Nickname+"] 님이 준비를 취소 하였습니다."); break;
		
		case "START": 
			chatAppend("게임이 곧 시작됩니다. 준비하세요!"); 
			setTimeout(startBingo,4000); 
			break;
		
		case "QUIT": 
			if(nickname==data.data.Nickname) location.href="/roomlist/";
			
			//chatAppend($('.user_'+data.data.Nickname+' span').text()+"님이 방에서 나갔습니다.");
			chatAppend('['+data.data.Nickname+"] 님이 방에서 나갔습니다.");
			$('.nick_'+data.data.Nickname).parent().remove(); break;
		
		case "BINGO_START": 
			$("#remaintime").css('display','none').next().css('display','none'); $("#turn").css('display','block');
			$("#bingoTable a").unbind("click").click(bingo);
			break;
		
		case "BINGO_CURRENT": 
			$("#turn > div").eq(0).text(data.data.CurrentNickname).end().eq(1).text(data.data.NextNickname);
			currentNickname = data.data.CurrentNickname;
			if(data.data.CurrentNickname==nickname) showMyTurn();
			break;
		
		case "BINGO_SELECT": 
			bingoUser = []; bingoEndUser = []; markSelect(data.data); break;
		
		case "BINGO_BINGO": 
			bingoUser.push(data.data.Nickname);
			message(bingoUser.join(", ")+"님이 한줄 이상을 완성 했습니다!"+(bingoEndUser.length>0?"<br />"+bingoEndUser.join(", ")+"님이 빙고를 완성 했습니다!":""));
			break;
		
		case "BINGO_LAST": 
			bingoUser.push(data.data.Nickname);
			message((bingoUser.length>0?bingoUser.join(", ")+"님이 빙고 한줄을 완성 했습니다!":"")+bingoEndUser.join(", ")+"님이 빙고를 완성 했습니다!");
			break;
		
		case "BINGO_END": 
			showResult(data.data.result); break;
		
		case "INSTANCE_EXIT": 
			setTimeout(goExit,10000); break;
		
		case "OK":
			switch(sendCmd){
				case "LOGIN": sendJoin(); break;
				case "JOIN": initJoin(); chatAppend("방에 접속하였습니다."); sendUserList(); break;
				case "READY": 	$("#ready_button").css('display','none'); 
								$("#already_button").css('display','block'); 
								break;
				case "UNREADY": $("#ready_button").css('display','block'); 
								$("#already_button").css('display','none'); 
								break;
			}
			break;
	}
}

function chatAppend(msg){
	var obj = $("#chat");
	obj.append("<br />"+msg);
	var scroll_position  = $("#chat").scrollTop();
	$("#chat").scrollTop(scroll_position+20);
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
	if(data.Message == "" | !data.Message)
		return ;
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
		var flag = false;
		$("#bingoTable a").each(function(){
			if($(this).text()=="x") flag = true;
		});
		if(flag==true){
			if(confirm("아직 작성하지 않은 빙고가 있습니다.\n입력을 완료 하시겠습니까?")){
				forceInsert();
			} else {
				return;
			}
		}
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
		else
			clearInterval(interval);
	}
}

function forceInsert(){
	var currentList = [];
	var idxList = [];
	var obj = $("#bingoTable a");
	obj.each(function(){
		currentList.push($(this).text());
	});
	var notInsertList = [];
	for(var a=1; a<=25; a++){
		var flag = false;
		for(var b=0,loopb=currentList.length; b<loopb; b++){
			if(currentList[b]==a) { flag = true; break; }
		}
		if(flag) continue;
		notInsertList.push(a);
	}
	obj.each(function(){
		if($(this).text()!="x") return;
		while(true){
			var idx = parseInt(Math.random()*(parseInt(currentList.length/10)+1)*10);
			var flag = false;
			for(var a=0,loopa=idxList.length; a<loopa; a++){
				if(idxList[a]==idx) { flag = true; break; }
			}
			if(flag) continue;
			if(notInsertList.length<idx) continue;
			if(!notInsertList[idx]) continue;
			idxList.push(idx);
			$(this).children('span').text(notInsertList[idx]);
			break;
		}
	}).attr('data-theme','c').trigger("create");
}

function forceStart(){
	if(owner != userid) return;
	sendCmd = "BINGO_START";
	send("BINGO_START",{});
	if(interval!=null)
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
	currentSelectTime2 = 14;
}

function sendSelectNumber(){
	var obj = $("#bingoTable");
	if(obj.find('a[data-theme=e]').size()==0){
		var max = obj.find('a[data-theme=c]').size();
		while(true){
			var idx = parseInt(Math.random()*(parseInt(max/10)+1)*10);
			if(max<idx) continue;
			obj.find('a[data-theme=c]').eq(idx).attr('data-theme','e');
			break;
		}
	}
	$("#bingoTable").trigger("create");
	var currentNumber = obj.find('a[data-theme=e]').text();
	var data = {};
	$("#remaintime").css('display','none');
	$("#okSelect").css('display','none');
	data.SelectedNumber = currentNumber;
	send("BINGO_SELECT",data);
}

function showMyTurn(){
	$("#okSelect").css('display','block');
	currentSelectTime2 = 0;
	$("#remaintime").css('display','block');
	$("#remaintime span").text(15);
	interval2 = setInterval(showTurnRemainTime,1000);
}

function showTurnRemainTime(){
	$("#remaintime span").text(15 - ++currentSelectTime2);
	if(currentSelectTime2>=15) {
		clearInterval(interval2);
		sendSelectNumber();
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
			if(addBingo>0){
				sendCmd = "BINGO_BINGO";
				send("BINGO_BINGO",{Bingo:addBingo});
			}
			currentBingo += addBingo;
			if(currentBingo>=bingoLine){
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
				list[a].result+'등'+
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



// dice game functions

var canvas_width ;	// canvas width
var canvas_height ;	// canvas height
var canvas_mid_pos_y ;	//coordinate X of canvas middle point
var canvas_mid_pos_x ;	//coordinate Y of canvas middle point
var square_side_length ;	//the length of square side
var square_distance ;		//the distance between squares
var dot_radius ; //dice dot radius
var ctx 		//canvas context	
var i ;
function draw_dice()
{
	//get device width and set canvas size as device width
	canvas_width  = document.documentElement.clientWidth -20 ;//get device width
	canvas_height = canvas_width;// get device height

	//set canvas size
	$("#dice_canvas").attr("width",canvas_width);
	$("#dice_canvas").attr("height",canvas_height);
	
	//calculate square size and distance between squares
	var side = 0 ;
	if(canvas_width > canvas_height)
		side = canvas_height ;
	else
		side = canvas_width ;
	
	square_distance = side / 10 ;
	square_side_length = side / 2 - square_distance ; 
	dot_radius = square_side_length / 9 ;
	
	//calculate coordinate X and Y of canvas middle point
	canvas_mid_pos_y = canvas_width / 2 ;
	canvas_mid_pos_x = canvas_height / 2 ;
	
	//calculate coordinate X and Y of canvas middle point
	canvas_mid_pos_y = canvas_width / 2 ;
	canvas_mid_pos_x = canvas_height / 2 ;
	
	ctx = document.getElementById("dice_canvas").getContext("2d");
	ctx.clearRect(0,0,canvas_width,canvas_height); // clear canvas			
	ctx.lineWidth= 5 ;
	
	var dice_num = Array() ;	//dice number 1~6

	for(  i = 0 ; i < 3 ; i++)
		dice_num[i] = Math.floor(Math.random() * 6 ) + 1; // get random number of three dices
	
	draw_sqrt_top_mid(dice_num[0]);
	draw_sqrt_btm_left(dice_num[1]);
	draw_sqrt_btm_right(dice_num[2]);
	var innerhtml = "<center>당신의 주사위의 합은 " + (dice_num[0] + dice_num[1] + dice_num[2]) + " 입니다.</center>";
	document.getElementById("cast_dice").innerHTML = innerhtml ;
	$("#dice_result").css("display","block")
}
function draw_sqrt_top_mid(dice_num)
{
	var X = canvas_width / 2 ;
	var Y = canvas_height / 4 ;
	draw_sqaure( X ,Y , square_side_length);
	draw_dice_dot(dice_num, X , Y , dot_radius );
}
function draw_sqrt_btm_left(dice_num) 
{
	var X = canvas_width / 4 ;
	var Y = canvas_height * 3 / 4 ;
	draw_sqaure( X ,Y , square_side_length );
	draw_dice_dot(dice_num, X , Y , dot_radius );
}
function draw_sqrt_btm_right(dice_num) 
{
	var X = canvas_width * 3 / 4 ;
	var Y = canvas_height * 3 / 4 ;
	draw_sqaure( X ,Y , square_side_length );
	draw_dice_dot(dice_num , X , Y , dot_radius );
}
function draw_sqaure( X ,Y , side_length)// X,Y are middle coordinate of square
{
	var sqrt_start_x = X - side_length / 2;
	var sqrt_start_y = Y - side_length / 2;
	ctx.fillStyle = "#4e93be";
	ctx.beginPath();
	ctx.rect( sqrt_start_x , sqrt_start_y , side_length , side_length);
	ctx.closePath();
	ctx.fill();
}
function draw_dice_dot( dice_number , X , Y , R )
{
	switch(dice_number)
	{
		case 1 : draw_dot_center_mid(X,Y,R); break;
		case 2 : draw_dot_top_left(X,Y,R); 
				 draw_dot_btm_right(X,Y,R); break;
		case 3 : draw_dot_top_right(X,Y,R); 
				 draw_dot_center_mid(X,Y,R); 
				 draw_dot_btm_left(X,Y,R); break;
		case 4 : draw_dot_top_left(X,Y,R);
				 draw_dot_top_right(X,Y,R);
				 draw_dot_btm_left(X,Y,R);
				 draw_dot_btm_right(X,Y,R); break;
		case 5 : draw_dot_top_left(X,Y,R);
				 draw_dot_top_right(X,Y,R);
				 draw_dot_center_mid(X,Y,R);
				 draw_dot_btm_left(X,Y,R);
				 draw_dot_btm_right(X,Y,R);break;
		case 6 : draw_dot_top_left(X,Y,R);
				 draw_dot_top_right(X,Y,R);
				 draw_dot_center_left(X,Y,R);
				 draw_dot_center_right(X,Y,R);
				 draw_dot_btm_left(X,Y,R);
				 draw_dot_btm_right(X,Y,R); break;
	}
}
function draw_dot_top_left( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X -= square_side_length / 4 ;
	Y += square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot_top_right( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X += square_side_length / 4 ;
	Y += square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot_center_left( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X -= square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot_center_right( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X += square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot_center_mid( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	draw_dot( X , Y , R )
}		
function draw_dot_btm_left( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X -= square_side_length / 4 ;
	Y -= square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot_btm_right( X , Y , R )//X,Y are square middle cooradinate , R is radius
{
	X += square_side_length / 4 ;
	Y -= square_side_length / 4 ;
	draw_dot( X , Y , R )
}
function draw_dot( X , Y , R )//X,Y are center coordinate of dot
{
	ctx.fillStyle = "#ed1c24";
	ctx.beginPath();
	ctx.arc(X,Y,R,0,Math.PI*2,true);
	ctx.closePath();
	ctx.fill();
}