<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<?php
$this->css('/static/style/bootstrap.min.css');
$this->js('/static/script/jquery.min.js');
$this->js('/static/script/bootstrap.min.js');
?>
<title>Student Manage System by Tamce</title>
</head>
<body>
<nav class="navbar navbar-default navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Student Manage System</a>
		</div>
		<div class="collapse navbar-collapse" id="navbar-collapse">
			<ul id="nav-all" class="nav navbar-nav">
				<li id="nav-home" class="active"><a href="#home">Home</a></li>
				<li id="nav-profile"><a href="#profile">Profile</a></li>
				<li id="nav-class"><a href="#class">Class Info</a></li>
			</ul>
			<form id="nav-btn" class="nav navbar-form navbar-right" onsubmit="return false;">
				<button class="btn btn-primary" onclick="window.location.hash='#login';">Login</button>
				<button class="btn btn-default" onclick="window.location.hash='#register';">Register</button>
			</form>
			<p class="navbar-text navbar-right" id="nav-user" style="display: none;">
				Signed in as <a href="#profile" class="navbar-link" id="nav-user-username"></a>
				&nbsp;&nbsp;
				<a href="javascript:logout();" class="navbar-link">Logout</a>
				&nbsp;&nbsp;
			</p>
		</div>
	</div>
</nav>
<div id="stage" class="container-fluid">
	<div id="stage-loading"><h1>Loading... Please wait...</h1></div>
	<div id="stage-home"></div>
	<div id="stage-profile"></div>
	<div id="stage-login"></div>
	<div id="stage-register"></div>
	<div id="stage-class"></div>
	<div id="stage-control"></div>
	<div id="stage-verify"></div>
</div>
<div class="footer" style="margin-top: 5%; margin-bottom: 2%; width: 80%; margin-left: 10%;">
<hr>
<center><small>
Copyright 2016 By Tamce<br>
All rights reserved.
<br>
</small></center>
</div>
<script>
// 此处通过 window 作为全局变量实现一个自定义的事件系统
window._sms={};
window._sms.loaded=[];
window._sms.stageTrigger = [];
window._sms.stage = "";
function changeStage(stage)
{
	$("li").removeClass("active");
	$("#nav-"+stage).addClass("active");
	if (window._sms.loaded[stage] == true) {
		$("#stage").children().css("display", "none");
		$("#stage-"+stage).fadeIn();
		$.each(window._sms.stageTrigger, function (index, callback) {
			callback(stage, window._sms.stage);
		});
		window._sms.stage = stage;
	} else {
		// Load stage
		$("#stage-loading").fadeIn();
		$("#stage-"+stage).load("/stage/"+stage, function () {
			window._sms.loaded[stage] = true;
			changeStage(stage);
		});
	}
}

function userLogin(data)
{
	window._sms.user = data;
	window._sms.login = true;
	$("#nav-btn").css("display", "none");
	$("#nav-user-username").text(window._sms.user.username);
	$("#nav-user").css("display", "block");
}

function userLogout()
{
	window._sms.user = [];
	window._sms.login = false;
	$("#nav-btn").css("display", "block");
	$("#nav-user").css("display", "none");
	if (window._sms.stage == "home") {
		changeStage("home");
	} else {
		window.location.hash = "#home";
	}
}

$(window).on("hashchange", function (e) {
	changeStage(window.location.hash.substr(1));
});

function logout()
{
	$.ajax("/api/logout", {
		method: "GET",
		success: function (data) {
			if (data.status == "success") {
				userLogout();
			} else {
				alert("An error occurred!\n" + data.info);
			}
		},
		error: function (xhr) {
			alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
		}
	})
}

// 执行初始化工作，如果会话未过期则直接登录
$.ajax("/api/user", {
	method: "GET",
	success: function (data) {
		if (data.status == "success") {
			userLogin(data.data);
		}
	}
});
/*
var hash = window.location.hash == "" ? "#home" : window.location.hash;
window.location.hash = "#";
window.location.hash = hash;
*/
if (window.location.hash == "") {
	window.location.hash = "#home";
} else {
	changeStage(window.location.hash.substr(1));
}
</script>
</body>
