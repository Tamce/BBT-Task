<script>
function login()
{
	$("#login-btn").attr("disabled", "disabled");
	$.ajax("/api/authorization", {
		data: $("#login-form").serialize(),
		method: "POST",
		success: function (data, status, xhr) {
			console.log(data);
			userLogin(data.data);
			window.location.hash = "#profile";
		},
		error: function (xhr) {
			if (xhr.getResponseHeader("Content-Type") != "application/json") {
				alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
				return;
			}
			$("#login-notice").text(xhr.responseJSON.info);
			$("#login-notice").fadeIn();
			setTimeout(function () {
				$("#login-notice").fadeOut();
			}, 5000);
		},
		complete: function () {
			$("#login-btn").attr("disabled", false);
		}
	});
}

window._sms.stageTrigger.push(function (stage) {
	$("#login-form input").val("");
	$("#login-btn").val("Login");
});
</script>
<div class="container-fluid">
	<div class="page-header">
		<h1>Student Manage System
		<small>Login</small></h1>
	</div>
	<div class="alert alert-danger" id="login-notice" style="display: none;"></div>
	<div class="col-md-4 col-md-offset-4">
	<form onsubmit="login(); return false;" id="login-form">
		<div class="form-group">
			<label class="control-label">Username:</label><input placeholder="Input your username" type="text" name="username" class="form-control" />
		</div>
		<div class="form-group">
			<label class="control-label">Password:</label><input placeholder="Input password" type="password" name="password" class="form-control" />
		</div>
		<input type="submit" id="login-btn" class="btn btn-primary btn-block" value="Login" />
	</form>
	</div>
</div>
