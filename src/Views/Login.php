<html>
<head>
<title>用户登陆</title>
<?php
$this->css('/static/style/bootstrap.min.css');
$this->js('/static/script/jquery.min.js');
$this->js('/static/script/common.js');
 ?>
</head>
<body>
<script>
function login()
{
	$.ajax({
		url: "/api/login",
		method: "POST",
		data: $("#login-form").serialize()
	}).done(function (data, status, xhr) {
		console.log(data);
		if (data.status == "success") {
			window.location.href = data.jump;
		} else {
			showError(data.info);
		}
	}).fail(function (xhr, status, text) {
		showError("An error occurred when pending data: \n" + xhr.status + "  " + text);
	});
}
</script>
<div class="container">
	<div class="page-header">
		<h1>School Roll Manage System&nbsp;&nbsp;<small>Login</small></h1>
	</div>
	<button onclick="window.location.href='/register'" class="btn btn-default">Register</button>
	<div id="login-box" class="col-md-offset-3 col-md-6" style="margin-top: 2%;">
		<form id="login-form" onsubmit="login(); return false;">
			<div class="form-group">
				<label class="control-label">Username: </label>
				<input type="text" name="username" class="form-control" />
			</div>
			<div class="form-group">
				<label class="control-label">Password: </label>
				<input type="password" name="password" class="form-control" />
			</div>
			<input type="submit" class="btn btn-default" style="width: 50%; margin-left: 25%;" value=" Login "/>
		</form>
	</div>
</div>
</body>
</html>
