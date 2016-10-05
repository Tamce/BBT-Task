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
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">Student Manage System</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul id="nav-all" class="nav navbar-nav">
				<li id="nav-home" class="active"><a href="#home">Home</a></li>
				<li id="nav-profile"><a href="#profile">Profile</a></li>
			</ul>
			<form id="nav-btn" class="nav navbar-form navbar-right" onsubmit="return false;">
				<button class="btn btn-primary" onclick="window.location.hash='#login';">Login</button>
				<button class="btn btn-default" onclick="window.location.hash='#register';">Register</button>
			</form>
		</div>
	</div>
</nav>
<div id="stage" class="container-fluid">
	<div id="stage-loading">Loading... Please wait...</div>
	<stage id="stage-home"></stage>
	<stage id="stage-profile"></stage>
</div>
<script>
window._sms={};
window._sms.loaded=[];
function changeStage(stage)
{
	$("li").removeClass("active");
	$("#nav-"+stage).addClass("active");
	if (window._sms.loaded[stage] == true) {
		$("stage").css("display", "none");
		$("#stage-"+stage).css("display", "block");
	} else {
		// Load stage
		$("#stage-loading").css("display", "block");
		$("#stage-"+stage).load("/stage/"+stage);
		window._sms.loaded[stage] = true;
		changeStage(stage);
	}
	$("#stage-loading").css("display", "none");
}

$(window).on("hashchange", function (e) {
	switch(window.location.hash.substr(1)) {
		case "profile":
			changeStage("profile");
			break;
		case "home":
			changeStage("home");
			break;
		default:
			break;
	}
});

changeStage("home");
</script>
</body>
