$(window).resize(function() {
	resizeContent();
});

$("div[data-role='page']").live( "pageshow", function( event )
{
	resizeContent();
});

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
			$("#gamedisplay").css("display" , "none");
		}
		else{
			$("#chat").css("display" , "block");
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

function init(){
  var host = "ws://localhost:12345/websocket/server.php";
  try{
    socket = new WebSocket(host);
    log('WebSocket - status '+socket.readyState);
    socket.onopen    = function(msg){ log("Welcome - status "+this.readyState); };
    socket.onmessage = function(msg){ log("Received: "+msg.data); };
    socket.onclose   = function(msg){ log("Disconnected - status "+this.readyState); };
  }
  catch(ex){ log(ex); }
}

function send(){
  var txt,msg;
  txt = $("msg");
  msg = txt.value;
  if(!msg){ alert("Message can not be empty"); return; }
  txt.value="";
  txt.focus();
  try{ socket.send(msg); log('Sent: '+msg); } catch(ex){ log(ex); }
}

function quit(){
  log("Goodbye!");
  socket.close();
  socket=null;
}

// Utilities
function log(msg){ $("#chat").innerHTML+="<br>"+msg; }