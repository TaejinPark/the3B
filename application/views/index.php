<!DOCTYPE html>
<html>
	<head>
		<title>The BokBulBok</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<link   href="/resource/js/jquery/jquery.mobile-1.1.0.css" rel="stylesheet"/>
		<script src ="http://code.jquery.com/jquery-1.6.4.min.js"></script>
		<script src ="/resource/js/jquery/jquery.mobile-1.1.0.min.js"></script>
		<script src ="/resource/js/T3B.js"></script>
		<link 	rel="stylesheet" href="/resource/css/T3B.css">
		<script>
		$("div[data-role='page']").live("pageshow",function(event){
			$('#join input').blur(vaildForm);
			load();
			$(".login").click(function(){view('login');});
			$(".join").click(function(){view('join');});
			$(".guest").click(function(){doGuestLogin();});
			$('#login form').submit(function(){
				doLogin($(this));
				return false;
			});
			$('#join form').submit(function(){
				doJoin($(this));
				return false;
			});
			$("#content").click(function(){view_clear();});
		});
		</script>
	</head>
	
	<body id="body" onload="load()">
	<div data-role="page" class="type-interior">
		
		<div id ="header" data-role="header" data-position="fixed" data-theme="b">
			<div class="padding menu">
				<span class="login">Login</span><span> | </span>
				<span class="join">Join </span><span> | </span>
				<span class="guest">Guest</span>
			</div>
		</div><!-- /header -->
		
		<div id="login" class="ui-body ui-body-d" >
			<form method="POST" action=".">
				<div class="input_field_div">
					<input type="text" name="id" value="" placeholder="사용자 ID"/>
				</div>
				<div class="input_field_div">
					<input type="password" name="pw" value="" placeholder="비밀번호" />
				</div>
				<div class="input_field_div">
					<input type="submit" value="확인">
				</div>
			</form>
		</div><!--/login-->	
		
		<div id="join" class="ui-body ui-body-d">
			<form method="POST" action=".">
				<div class="input_field_div">
					<input type="text" name="id" value="" placeholder="사용자 ID" maxlength="100"/>
					<span></span>
				</div>
				<div class="input_field_div">
					<input type="password" name="pw" value="" placeholder="비밀번호" maxlength="32"/>
				</div>
				<div class="input_field_div">
					<input type="password" name="pw_verify" value="" placeholder="비밀번호 확인"  maxlength="32"/>
					<span></span>
				</div>
				<div class="input_field_div">
					<input type="text" name="nick_name" value="" placeholder="사용자 별칭" maxlength="100"/>
					<span></span>
				</div>
<!--				<div>생년월일</div>
				<div name="birth_year" class="select_field_year">
					<select name="year" id="year">
							<option value="1970">1970</option>
					</select>
				</div>
				<div name="birth_month" class="select_field_month">
					<select name="month" id="month">
							<option value="12">12</option>
					</select>
				</div>
				<div name="birth_day" class="select_field_day">
					<select name="day" id="day">
							<option value="31">31</option>
					</select>	
				</div>-->
				<div class="submit">
					<input type="submit" value="확인">
				</div>
			</form>
		</div><!--/join-->	
				
		<div data-role="content" data-theme="b" id="content">
			<img src="/resource/img/main.jpg" width="100%" height="100%"/>
		</div><!-- /content -->
				
		<div data-role="footer" data-position="fixed" data-theme="b" class="padding" id="footer">
			<div>The BokBulBok</div>
		</div><!-- /footer -->
		
	</div><!-- /page -->
	</body>
</html>
