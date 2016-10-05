<div>
<div>
<h2>Welcome to Student Manage System!</h2>
<div class="col-md-6 col-md-offset-3">
	<div id="avatar" class="col-md-4 col-md-offset-4" style="text-align: center;"></div>
	<table class="table table-condensed">
		<thead>
		<tr>
		<th colspan="2">Profile<span style="float:right; cursor: hand; cursor: pointer;"
			class="glyphicon glyphicon-pencil" onclick="$('#modal-edit-profile').modal('show');"></span></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>User Group</td>
			<td id="profile-userGroup"></td>
		</tr>
		<tr>
			<td>Name</td>
			<td id="profile-name"></td>
		</tr>
		<tr>
			<td>Gender</td>
			<td id="profile-gender"></td>
		</tr>
		<tr>
			<td>Class</td>
			<td id="profile-class"></td>
		</tr>
		</tbody>
	</table>
	<div id="admin-only" class="alert alert-info">
		<p>As an Admin, you can goto: <a class="alert-link" href="#control">Control Pannel</a></p>
	</div>
</div>
</div>
<div class="modal fade" id="modal-edit-profile" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="modal-title">Edit Profile</h4>
			</div>
			<div class="modal-body">
				<form id="profile-form">
					<div class="form-group">
						<label>Username: &nbsp;<span id="modal-username"></span></label>
					</div>
					<div class="form-group">
						<label>User Group: &nbsp;<span id="modal-userGroup"></span></label>
					</div>
					<div class="form-group">
						<label>Name:</label><input placeholder="Input your real name..." id="modal-name" name="name" class="form-control" />
					</div>
					<div class="form-group">
						<label>Gender:</label>
						<select class="form-control" id="modal-gender" name="gender">
							<option value="male">male</option>
							<option value="femail">female</option>
							<option value="other">other</option>
						</select>
					</div>
					<div class="form-group">
						<!--TODO 注意这里要特殊处理，因为教师的 class 字段是数组-->
						<label>Class:</label>
						<select class="form-control" id="modal-class" name="classname">
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="changeProfile();">Save changes</button>
			</div>
		</div>
	</div>
</div>
<div>
<script>
function _profile()
{
	if (window._sms.login != true) {
		alert("Please Login First!");
		window.location.hash = "#login";
		return;
	}

	// 更新班级列表
	$.ajax("/api/class", {
			method: "GET",
			success: function (data) {
				$("#modal-class").html("");
				$.each(data.data, function (i, v) {
					$("#modal-class").append("<option value=\""+v.classname+"\">"+v.classname+"</option>");
				});
			},
			error: function (xhr) {
				alert("An error occurred when fetching class info!\n" + xhr.status + " " + xhr.statusText);
			}
		});

	// 按照用户组决定班级是否多选
	if (window._sms.user.userGroup == 2) {
		$("#modal-class").attr("multiple", "multiple");
	} else {
		$("#modal-class").attr("multiple", false);
	}

	if (window._sms.user.userGroup == 3) {
		$("#admin-only").css("display", "block");
	} else {
		$("#admin-only").css("display", "none");
	}

	if (window._sms.user.newUser == "1") {
		// 该用户是新注册的用户，提示补全信息
		alert("Please complete your information first!");
		$("#modal-username").text(window._sms.user.username);
		$("#modal-userGroup").text(window._sms.user.userGroup == 1 ? "Student" : window._sms.user.userGroup == 2 ? "Teacher" : "Admin");
		$("#modal-edit-profile").modal("show");
	}

	$("#avatar").load("/api/user/avatar");
	syncInfo();
}

function syncInfo()
{
	$("#modal-username").text(window._sms.user.username);
	$("#modal-userGroup").text(window._sms.user.userGroup == 1 ? "Student" : window._sms.user.userGroup == 2 ? "Teacher" : "Admin");
	$("#modal-name").val(window._sms.user.name);
	$("#modal-gender").val(window._sms.user.gender);

	$("#profile-name").text(window._sms.user.name);
	$("#profile-gender").text(window._sms.user.gender);
	$("#profile-userGroup").text(window._sms.user.userGroup == 1 ? "Student" : window._sms.user.userGroup == 2 ? "Teacher" : "Admin");
	if (window._sms.user.userGroup == 2) {
		$("#profile-class").text("");
		$.each(window._sms.user.classname, function (index, value) {
			$("#profile-class").append(value + "<br>");
		});
	} else {
		$("#profile-class").text(window._sms.user.classname);
	}
}

function changeProfile()
{
	$.ajax("/api/user", {
		method: "PATCH",
		data: $("#profile-form").serialize(),
		success: function (data) {
			if (data.status == "success") {
				window._sms.user = data.data;
				syncInfo();
			} else {
				alert(data.info);
			}
			$("#modal-edit-profile").modal("hide");
		},
		error: function (xhr) {
			alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
		}
	});
}

window._sms.stageTrigger.push(function (stage) {
	if (stage == "profile") {
		_profile();
	}
});
</script>
