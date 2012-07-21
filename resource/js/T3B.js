//page view control

$(window).resize(function() {
	viewRoomListInit();
	traceFormPosition();
	resizeContent();
});

$("div[data-role='page']").live( "pageshow", function( event )
{
	viewRoomListInit();
    resizeContent();
});

function resizeContent()
{
	var header_obj = $("div[data-role='header']") ;
	var footer_obj = $("div[data-role='footer']") ;
	var browserHeight = document.documentElement.clientHeight;
    var headerHeight = parseInt( header_obj.height())+parseInt(header_obj.css("padding-bottom"))+parseInt(header_obj.css("padding-top"))+parseInt(header_obj.css("border-top-width"))+parseInt(header_obj.css("border-bottom-width"));
    var footerHeight = parseInt(footer_obj.height())+parseInt(footer_obj.css("padding-bottom"))+parseInt(footer_obj.css("padding-top"))+parseInt($("#footer").css("border-bottom-width"))+parseInt(footer_obj.css("border-top-width"));
    
    // set content size as browswer heigt - fixed header height
    if(navigator.userAgent.indexOf('iPhone') != -1 || navigator.userAgent.indexOf('iphone') != -1)
		$("#content").css("height" , browserHeight - headerHeight + 60); // if browser is iphone , content height becomes more higher than PC browser
	else // normal browser
		$("#content").css("height" , browserHeight - headerHeight );
}

var buttonFlag = false ;	
var interval;
function traceFormPosition(){
	interval = setInterval(formPosition,1);
};

// url : index

function formPosition(){ // set form position about "login" and "join" input 
	var Y = getNowScroll().Y;
	var height;
	var obj;
	obj = document.getElementById("header");
	height = $(obj).height();
	obj = document.getElementById("join");
	if(!obj){
		clearInterval(interval);
		return;
	}
	obj.style.top = Y+height+'px';
	obj = document.getElementById("login");
	obj.style.top = Y+height+'px';
}

function getNowScroll(){ // get current x,y coordinate of scroll-bar position 
	var de = document.documentElement;
	var b = document.body;
	var now = {};
	now.X = document.all ? (!de.scrollLeft ? b.scrollLeft : de.scrollLeft) : (window.pageXOffset ? window.pageXOffset : window.scrollX);
	now.Y = document.all ? (!de.scrollTop ? b.scrollTop : de.scrollTop) : (window.pageYOffset ? window.pageYOffset : window.scrollY);
	return now;			
}

function view_join_login(element){ // disply / non-display to join,login form
	var obj = document.getElementById("join");
	obj.style.display = "none";
	obj = document.getElementById("login");
	obj.style.display = "none";
	obj = document.getElementById(element);
	if(obj.style.display == "none")
		obj.style.display = "block";
	else
		obj.style.display = "none";
	buttonFlag = true;
}

function view_clear(){ // non-display to join, login form
	document.getElementById("join").style.display = "none";
	document.getElementById("login").style.display = "none";
}


function doLogin(obj){
	// call "doLogin" function of index.php file in controller directory
	$.ajax({type:'POST',url:"/index/doLogin/",data:{userID:obj.find('input[name=id]').val(),password:obj.find('input[name=pw]').val()}})
	.done(function(data){
		if(data=="false") 				// login fail
			alert("사용자 ID가 잘못되었거나, 비밀번호가 잘못되었습니다.");
		else if(data=='existslogin'){	// already logined
			alert("이미 로그인 되어 계시네요!\n방 목록 페이지로 이동합니다~");
			location.href="/roomlist/";
		} else if(data=="true")			// login success
			location.href="/roomlist/";
	});
}

function vaildForm(){ // check input values
	var obj = $(this);
	var spanobj = obj.next('span');
	if(!obj.val()){
		spanobj.text('불가능');
		return;
	}
	switch(obj.attr('name')){
		case 'id':
			// call "isExistID" function of index.php file in controller directory
			$.ajax({type:"POST",url:"/index/isExistID/",data:{userID:obj.val()}}).done(function(data){
				if(data=="false") spanobj.text('불가능');
				else if(data=="true") spanobj.text('가능');
			});
		break;
		case 'pw_verify':
			if($('#join input[name=pw]').val() != obj.val() ) spanobj.text('불가능');
			else spanobj.text('가능');
		break;
		case 'nick_name':
			$.ajax({type:"POST",url:"/index/isExistNickname/",data:{nickname:obj.val()}})
			.done(function(data){
				if(data=="false") spanobj.text('불가능');
				else if(data=="true") spanobj.text('가능');
			});
		break;
	}
}

function doJoin(form){
	var obj = $(form);
	var id = obj.find('input[name=id]').val();
	var pw = obj.find('input[name=pw]').val();
	var nickname = obj.find('input[name=nick_name]').val();
	$.ajax({type:"POST",url:"/index/doJoin/",data:{userID:id,password:pw,nickname:nickname}})
	.done(function(data){
		if(data=="false")
			alert("정보가 잘못 입력되었습니다.\n입력 한 정보를 다시 입력해 주세요.");
		else if(data=='existsjogin'){
			alert("이미 로그인 되어 계시네요!\n방 목록 페이지로 이동합니다~");
			location.href="/roomlist/";
		} else if(data=="true") {
			alert("가입이 완료 되었습니다.\n로그인 버튼을 눌러 로그인 해 주세요.");
			view_clear();
			view('login');
		}
	});
}

function doGuestLogin(){
	$.ajax({type:"POST",url:"/index/doGuestLogin/"})
	.done(function(data){
		if(data=='existslogin'){
			alert("이미 로그인 되어 계시네요!\n방 목록 페이지로 이동합니다~");
			location.href="/roomlist/";
		} else if(data=="true") {
			location.href="/roomlist/";
		}
	});
}

// url: /roomlist/

function viewRoomListInit(){
	var obj = $("#make_icon");
	var width = $("#make_icon").children("span").width();
	var obj2 = $("#find_room_by_type");

	if(navigator.userAgent.indexOf('Windows') != -1)
		obj2.css("width" , $("#content").width() - width -30);
	else
		obj2.css("width" , $("#content").width() - width -15);
	obj.width(obj.children('span').width());
	
	obj = $("#refresh_icon");
	width = obj.children("span").width();
	obj2 = $("#search_rooms");
	if(navigator.userAgent.indexOf('Windows') != -1)
		obj2.css("width" , $("#content").width() - width -30);
	else
		obj2.css("width" , $("#content").width() - width -15);
	obj.width(obj.children("span").width());
}

function viewRoomListMenu(element){
	$("#roomlist").css("display","none");
	$("#status").css("display", "none");
	$("#makeroom").css("display", "none");
	var obj = document.getElementById(element);
	
	if(obj.style.display == "none")
		obj.style.display = "block";
	else
		obj.style.display = "none";
	if(element=="status"){
		loadUserStatus();
	}
	buttonFlag = true;
}

function loadRoomList(start){
	var roomstr = '';
	$.ajax({url:"/roomlist/getRoomListToJson/",data:{start:start,keyword:$('input[name=search]:eq(0)').val(),type:$('select[name=find_room_by_type]').val()}})
	.done(function(data){
		if(!data) return;
		var list = eval(data);
		if(list.length==0) $('#roomlist > a').css('display','none');
		var str = '';
		for(var a=0,loopa=list.length; a<loopa; a++){
			str +='<div data-role="collapsible" data-collapsed="true" data-content-theme="e">'+
				'<h3>'+
					'[<span class="roomnumber">'+list[a].room_seq+'</span>]<span> '+list[a].name+'</span>'+ // room sequence
					'<span class="gametype">'+
					'[<span> '+list[a].currentuser+' / '+list[a].maxuser+' </span>]&nbsp;'+ // user number
					'<img src="/resource/img/'+(parseInt(list[a].start) ? 'playing' : 'waiting')+'_icon.png"/>&nbsp;'; // playing / non-playing
				
			switch(list[a].gametype){
				case "빙고" : str += '<img src="/resource/img/bingo_icon.png"/>'; break;
				case "주사위" : str += '<img src="/resource/img/dice_icon.png"/>'; break;
				case "사다리" : str += '<img src="/resource/img/ladder_icon.png"/>'; break;
				case "해적" : str += '<img src="/resource/img/pirate_icon.png"/>'; break;
			}
				str+=	'&nbsp;'+
						'<img src="/resource/img/'+(parseInt(list[a]["private"]) ? 'lock':'unlock')+'_icon.png"/>'+
					'</span>'+
				'</h3>'+
				'<p>'+
					'<div>참가자 : '+
						'<span>'+list[a].currentuser+'</span>/'+
						'<span>'+list[a].maxuser+'</span>'+
					'</div>'+
					'<div>게임 종류 : '+
						'<span>'+list[a].gametype+'</span>'+
					'</div>'+
					'<div>게임 옵션 : ';
			switch(list[a].gametype){
				case "빙고" : str += '승리 빙고 : '+'<b>' + list[a].gameoption+'줄 </b>' ; break;
				case "주사위" : str += '주사위 숫자가 큰 사람이 <b>' + (list[a].gameoption ? '승리' : '패배') +'</b>'; break; 
				case "사다리" : str += '방 접속시 공개' ; break;
				case "해적" : str += '당첨 칼을 꽂는 사람이 <b>' + (list[a].gameoption ? '승리' : '패배') + '</b>'; break; 
			}
			str +=	'</div>'+ 
					'<div>';
			if(!parseInt(list[a].start))
				str +='<span class="join" data-role="button" data-theme="a" data-icon="star">방 참가</span>';
			str += '</div>'+
				'</p>'+
			'</div>';
		}
		if(start==0) {
			$('#RoomList').html(str).parent().trigger("create");
			$('#roomlist > a').css('display','block');
		} else $('#RoomList').append(str).parent().trigger("create");
		$('#RoomList .join').unbind('click').click(function(){
			location.href="/game/index/"+$(this).parent().parent().parent().find('.roomnumber').text()+'/';
		});
	});
}

function makeRoom(obj){
	var data = {};
	obj.find('input, select').each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	if(!data.name || data.name=="") {
		alert("방 이름을 입력해 주세요");
		obj.find('input[name=name]').focus();
		return;
	}
	$.ajax({type:"POST",url:"/roomlist/doMakeRoom/",data:data}).done(function(data){
		switch(data){//data is room number
			case '-1': 	alert("방 이름을 입력해 주세요.");
				obj.find('input[name=name]').focus();
				break;
			case '0':
				alert('방 정보가 잘못 되었습니다.');
				break;
			default:
				location.href="/game/index/"+data+'/';
		}
	});
}

function loadUserStatus(){ // not yet implementation
	$.ajax({url:"/roomlist/getUserInfo/"}).done(function(data){
		if(!data) return;
		var tmp = eval(data);
		tmp=tmp[0];
		$('#statusUserID').html(tmp['id']);
		$('#statusNickname').html(tmp['nickname']);
		$('#statusTotal').html(tmp['total']);
		$('#statusWin').html(tmp['win']);
		$('#statusLose').html(tmp['total'] - tmp['win']);

		$('#bingoTotal').html(tmp['total']);
		$('#bingoWin').html(tmp['win']);
		$('#bingoLose').html(tmp['total'] - tmp['win']);

		$('#diceTotal').html(tmp['total']);
		$('#diceWin').html(tmp['win']);
		$('#diceLose').html(tmp['total'] - tmp['win']);

		$('#ladderTotal').html(tmp['total']);
		$('#ladderWin').html(tmp['win']);
		$('#ladderLose').html(tmp['total'] - tmp['win']);

		$('#pirateTotal').html(tmp['total']);
		$('#pirateWin').html(tmp['win']);
		$('#pirateLose').html(tmp['total'] - tmp['win']);
	});
}

function doLogout(){
	$.ajax({url:"/roomlist/doLogout/"})
	.done(function(){
		alert("로그아웃 되었습니다~");
		location.href="/";
	});
}


function viewGameOption(value){
	$("#game_1").css("display","none");
	$("#game_2").css("display","none");
	$("#game_3").css("display","none");
	$("#game_4").css("display","none");
	$("#game_"+value).css("display","block");
}