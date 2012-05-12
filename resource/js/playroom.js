$(window).resize(function() {
	resizeContent();
	resizeContent2();
});

$("div[data-role='page']").live( "pageshow", function( event )
{
	resizeContent();
	resizeContent2();
});

function resizeContent()
{
	var headerHeight = parseInt( $("div[data-role='header']").css( "height" ) );
	var footerHeight = parseInt( $("div[data-role='footer']").height());
	var contentHeight = $("#content").css.height - headerHeight - footerHeight;
	$("div[data-role='content']").css( "height", contentHeight );
}

function resizeContent2(){
	var browserHeight = document.documentElement.clientHeight;
	var browserWidth = document.documentElement.clientWidth;
	$("#chat").css("height" , browserHeight);
	$("#gamedisplay").css("height" , browserHeight)
	$("#participant_list").css("height" , browserHeight);
	$("#chat").css("width" , browserWidth -10);
	$("#button_list").css("width" , browserWidth-10);
	$("#chat_input").css("width" , browserWidth - $("#chat_send").width() - 30);
}

function view(id){
	$("#chat").css("display" , "none");
	$("#participant_list").css("display" , "none");
	$("#gamedisplay").css("display" , "none");
	$("#"+id).css("display" , "block");
}
