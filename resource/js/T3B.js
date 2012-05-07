$(window).resize(function() {
	init();
	load();
});

$("div[data-role='page']").live( "pageshow", function( event )
{   
    resizeContent();
    init();
    load();
});


function resizeContent()
{
    var headerHeight = parseInt( $("div[data-role='header']").css( "height" ) );
    var footerHeight = parseInt( $("div[data-role='footer']").height());
    var contentHeight = $("#content").css.height - headerHeight - footerHeight;
    $("div[data-role='content']").css( "height", contentHeight );
}

//////////////////////////////////////////////////////////////////////////////////////////

var buttonFlag = false;	

function load(){
	birth_option();
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
	var width = obj.children("span").width();
	var obj2 = $("#select_game_type");
	obj2.width($("#content").width() - width -15);
	obj.width(obj.children('span').width());
	
	obj = $("#refresh_icon");
	width = obj.children("span").width();
	obj2 = $("#search_rooms");
	obj2.width($("#content").width() - width -15);
	obj.width(obj.children("span").width());
}

function view_room_stat(element){
	var obj = document.getElementById("roomlist");
	obj.style.display = "none";
	obj = document.getElementById("status");
	obj.style.display = "none";
	obj = document.getElementById(element);
	if(obj.style.display == "none")
		obj.style.display = "block";
	else
		obj.style.display = "none";
	buttonFlag = true;
}