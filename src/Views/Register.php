<div class="container-fluid">
	<div class="page-header">
		<h1>Student Manage System
		<small>Register</small></h1>
	</div>
	<div class="alert alert-danger" id="register-notice" style="display: none;"></div>
	<div class="col-md-6 col-md-offset-3">
	<form onsubmit="register(); return false;" id="register-form">
		<div class="form-group">
			<label class="control-label"><span class="text-danger">*&nbsp;</span>Username:</label><input tpye="text" name="username" class="form-control" />
		</div>
		<div class="form-group">
			<label class="control-label"><span class="text-danger">*&nbsp;</span>Password:</label><input type="password" name="password" class="form-control" />
		</div>
		<div id="key" class="form-group" style="display: none;">
			<label class="control-label"><span class="text-danger">*&nbsp;</span>Key:&nbsp;</label>
			<input type="text" name="key" class="form-control" />
		</div>
		<div class="form-group" id="user-group">
			<label class="col-md-4"><input id="ug-s" type="radio" name="userGroup" value="1" checked />&nbsp;Student</label>
			<label class="col-md-4"><input id="ug-t" type="radio" name="userGroup" value="2" />&nbsp;Teacher</label>
			<label class="col-md-4"><input id="ug-a" type="radio" name="userGroup" value="3" />&nbsp;Admin</label>
		</div>
		<input type="submit" id="register-btn" class="btn btn-primary btn-block" value="Register" />
	</form>
	</div>
</div>
<script>
$("#user-group input").on("click", function (e) {
	if ($(e.target).val() != 1) {
		$("#key").css("display", "block");
	} else {
		$("#key").css("display", "none");
	}
});

window._sms.stageTrigger.push(function (stage) {
	$("#rigister-form input").val();
	$("#rigister-btn").val("Register");
});

function register()
{
	$("#register-btn").attr("disabled", "disabled");
	$.ajax("/api/users", {
		method: "POST",
		data: $("#register-form").serialize(),
		success: function (data) {
			console.log(data);
			if (data.status == "success") {
				userLogin(data.data);
				window.location.hash = "#profile";
			} else {
				$("#register-notice").text(data.info);
				$("#register-notice").fadeIn();
				setTimeout(function () {
					$("#register-notice").fadeOut();
				}, 5000);
			}
		},
		error: function (xhr) {
			if (xhr.getResponseHeader("Content-Type") != "application/json") {
				alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
				return;
			}
			$("#register-notice").text(xhr.responseJSON.info);
			$("#register-notice").fadeIn();
			setTimeout(function () {
				$("#register-notice").fadeOut();
			}, 5000);
		},
		complete: function () {
			$("#register-btn").attr("disabled", false);
		}
	});
}
</script>
