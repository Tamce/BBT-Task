<html>
<head>
<title>用户注册</title>
<?php
$this->css('/static/style/bootstrap.min.css');
$this->js('/static/script/jquery.min.js');
$this->js('/static/script/common.js');
 ?>
</head>
<body>
<script>
function register()
{
	console.log($("#register-form").serialize());
	$.ajax({
		url: "/api/rigister",
		method: "POST",
		data: $("#register-form").serialize()
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
		<h1>School Roll Manage System&nbsp;&nbsp;<small>Register</small></h1>
	</div>
	<button onclick="window.location.href='/login'" class="btn btn-default">Login</button>
	<div id="register-box" class="col-md-offset-3 col-md-6" style="margin-top: 2%;">
		<form id="register-form" onsubmit="register(); return false;">
			<div class="form-group">
				<label class="control-label"><font color=red>*</font>&nbsp;Username: </label>
				<input type="text" name="username" class="form-control" />
			</div>
			<div class="form-group">
				<label class="control-label"><font color=red>*</font>&nbsp;Password: </label>
				<input type="password" name="password" class="form-control" />
			</div>
			<div>
				<div class="col-sm-4 col-sm-offset-2">
					<label><input type="radio" name="userGroup" value="1" checked />&nbsp;Student</label>
				</div>
				<div class="col-sm-4 text-right">
					<label><input type="radio" name="userGroup" value="2" />&nbsp;Teacher</label>
				</div>
			</div>
			<input type="submit" class="btn btn-default" style="width: 50%; margin-left: 25%;" value=" Register "/>
		</form>
	</div>
</div>
</body>
</html>
