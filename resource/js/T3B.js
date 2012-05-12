$(window).resize(function() {
	init();
	load();
});

$("div[data-role='page']").live( "pageshow", function( event )
{   
    resizeContent();
    init();
});

function resizeContent()
{
	var browserHeight = document.documentElement.clientHeight;
    var headerHeight = parseInt( $("div[data-role='header']").css( "height" ) );
    var footerHeight = parseInt( $("div[data-role='footer']").height());
    var contentHeight = $("#content").css("height", browserHeight - headerHeight - footerHeight);
    if(navigator.userAgent.indexOf('iPhone') != -1 || navigator.userAgent.indexOf('iphone') != -1){
		$("#content").css("height" , browserHeight - headerHeight - footerHeight +60);
	}
	else
		$("#content").css("height" , browserHeight - headerHeight - footerHeight);
}

//////////////////////////////////////////////////////////////////////////////////////////

var buttonFlag = false;	

function load(){
	//birth_option();
	setInterval(formPosition,1);
}

function birth_option(){
	var innerhtml="";
	var i,j;
	for(i=1970 ; i<=2012;i++)
		innerhtml += '<option value='+i+'>'+i+'</option>';
	document.getElementById('year').innerHTML = innerhtml;
	
	innerhtml ="";
	for(i=1 ; i<=12;i++)
		innerhtml += '<option value='+i+'>'+i+'</option>';
	document.getElementById('month').innerHTML = innerhtml;
	
	innerhtml ="";
	for(i=1 ; i<=31;i++)
		innerhtml += '<option value='+i+'>'+i+'</option>';
	document.getElementById('day').innerHTML = innerhtml;
}

function formPosition(){
	var Y = getNowScroll().Y;
	var height;
	var obj;	
	obj = document.getElementById("header");
	height = $(obj).height()
	obj = document.getElementById("join");
	obj.style.top = Y+height+'px';
	obj = document.getElementById("login");
	obj.style.top = Y+height+'px';
}

function getNowScroll(){
	var de = document.documentElement;
	var b = document.body;
	var now = {};
	now.X = document.all ? (!de.scrollLeft ? b.scrollLeft : de.scrollLeft) : (window.pageXOffset ? window.pageXOffset : window.scrollX);
	now.Y = document.all ? (!de.scrollTop ? b.scrollTop : de.scrollTop) : (window.pageYOffset ? window.pageYOffset : window.scrollY);
	return now;			
}

function view(element){
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

function view_clear(){
	var obj = document.getElementById("join");
	obj.style.display = "none";
	obj = document.getElementById("login");
	obj.style.display = "none";
}


/////////////////////////////////////////////////////////////////

function init(){
	var obj = $("#make_icon");
	var width = $("#make_icon").children("span").width();
	var obj2 = $("#select_game_type");
	obj2.css("width" , $("#content").width() - width -15);
	obj.width(obj.children('span').width());
	
	obj = $("#refresh_icon");
	width = obj.children("span").width();
	obj2 = $("#search_rooms");
	obj2.css("width" ,$("#content").width() - width -15);
	obj.width(obj.children("span").width());
	
}

function view_room_list(element){
	var obj = document.getElementById("roomlist");
	obj.style.display = "none";
	obj = document.getElementById("status");
	obj.style.display = "none";
	obj = document.getElementById("makeroom");
	obj.style.display = "none";
	obj = document.getElementById(element);
	
	if(obj.style.display == "none")
		obj.style.display = "block";
	else
		obj.style.display = "none";
	if(element=="status"){
		loadUserStatus();
	}
	buttonFlag = true;
}

// url: /index/

function doLogin(obj){
	$.ajax({type:'POST',url:"/index/doLogin/",data:{userID:obj.find('input[name=id]').val(),password:obj.find('input[name=pw]').val()}})
	.done(function(data){
		if(data=="false")
			alert("사용자 ID가 잘못되었거나, 비밀번호가 잘못되었습니다.");
		else if(data=='existslogin'){
			alert("이미 로그인 되어 계시네요!\n방 목록 페이지로 이동합니다~");
			location.href="/roomlist/";
		} else if(data=="true")
			location.href="/roomlist/";
	});
}

function vaildForm(){
	var obj = $(this);
	var spanobj = obj.next('span');
	if(!obj.val()){
		spanobj.text('불가능');
		return;
	}
	switch(obj.attr('name')){
		case 'id':
			$.ajax({type:"POST",url:"/index/isExistID/",data:{userID:obj.val()}})
			.done(function(data){
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
		else if(data=='existslogin'){
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

function loadRoomList(start){
	var roomstr = '';
	$.ajax({url:"/roomlist/getRoomListToJson/",data:{start:start,keyword:$('input[name=search]:eq(0)').val(),type:$('select[name=select_game_type]').val()}})
	.done(function(data){
		if(!data) return;
		var list = eval(data);
		var str = '';
		for(var a=0,loopa=list.length; a<loopa; a++){
			str +='<div data-role="collapsible" data-collapsed="true" data-content-theme="e">'+
				'<h3>'+
					'<span>'+list[a].name+'</span>'+
					'<img src="/resource/img/'+(list[a]["private"]?'lock':'unlock')+'_icon.png" width="24px" height="24px" style="float:right; margin:0px 5px;">'+
					'<span style="float:right; margin:0px 5px;"> '+
							'[<span>'+list[a].currentuser+'</span>/'+
							'<span>'+list[a].maxuser+'</span>]'+
					'</span>'+
					'<span style="float:right; margin:0px 5px;">'+list[a].gametype+'</span>'+
				'</h3>'+
				'<p>'+
					'<div>참가자 : '+
						'<span>'+list[a].currentuser+'</span>/'+
						'<span>'+list[a].maxuser+'</span>'+
					'</div>'+
					'<div>게임 종류 : '+
						'<span>'+list[a].gametype+'</span>'+
					'</div>'+
					'<div>-Option-<br>'+
						'승리 빙고 : '+
						'<span>'+list[a].gameoption+'줄</span>'+
					'</div>'+
					'<div>'+
						'<span data-role="button" data-theme="a" data-icon="star" style="padding:0px;margin:0px;">'+
						'방 참가'+
						'</span>'+
					'</div>'
				'</p>'+
			'</div>';
		}
		$('#RoomList').html(str).parent().trigger( "create" );
	});
}

function makeRoom(obj){
	var data = {};
	obj.find('input, select').each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	if(!data.name || data.name=="") {
		alert("방 이름을 입력해 주세요");
		obj.children('input[name=name]').focus();
		return;
	}
	$.ajax({type:"POST",url:"/roomlist/doMakeRoom/",data:data})
	.done(function(data){
		switch(data){
			case '-1': 
				alert("방 이름을 입력해 주세요.");
				obj.children('input[name=name]').focus();
				break;
			case '0':
				alert('방 정보가 잘못 되었습니다.');
				break;
			default:
				location.href="/room/"+data+'/';
		}
	});
}

function loadUserStatus(){
	$.ajax({url:"/roomlist/getUserInfo/"})
	.done(function(data){
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
	});
}

function doLogout(){
	$.ajax({url:"/roomlist/doLogout/"})
	.done(function(){
		alert("로그아웃 되었습니다~");
		location.href="/";
	});
}