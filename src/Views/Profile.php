<?php
if (!isset($login) or !$login) {
?>
<script>
window.location.href = "/login";
</script>
<?php
}
?>
<html>
<head>
<title>用户信息</title>
<?php
$this->css('/static/style/bootstrap.min.css');
$this->js('/static/script/jquery.min.js');
$this->js('/static/script/bootstrap.min.js');
$this->js('/static/script/common.js');
 ?>
</head>
<body>
<script>
window.user = <?=json_encode($user);?>;
function openModal()
{
	$("#modal-name").val(window.user.info.name);
	$("#modal-gender").val(window.user.info.gender);
	$("#modal-grade").val(window.user.info.grade);
	$("#modal").modal("show");
}

function updateInfo()
{
	$("#data-group").text(window.user.userGroup == 1 ? "Student" : "Teacher");
	$("#data-username").text(window.user.username);
	$("#data-name").text(window.user.info.name);
	$("#data-gender").text(window.user.info.gender);
	$("#data-grade").text(window.user.info.grade);
}

function update()
{
	$.ajax({
		url: "/api/update",
		method: "POST",
		data: $("#modal-form").serialize()
	}).done(function (data) {
		console.log(data);
		window.user = data;
		updateInfo();
	}).fail(function (xhr, status, text) {
		showError("An error occurred when pending data:\n" + xhr.status + " " + text);
		return;
	});
	$("#modal").modal("hide");
}
</script>
<div class="container">
	<div class="page-header">
		<h1>School Roll Manage System&nbsp;&nbsp;<small>Profile</small></h1>
	</div>
	<div>
	<h2>Welcome! <span id="data-group"></span></h2>
	Username: <span id="data-username"></span><br>
	Name: <span id="data-name"></span><br>
	Gender: <span id="data-gender"></span><br>
	Grade: <span id="data-grade"></span><br>
	<button onclick="openModal();" class="btn btn-default">Edit</button>
	<button onclick="window.location.href='/logout'" class="btn btn-default">Logout</button>
	</div>


	<div class="modal fade" id="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Edit Info</h4>
				</div>
				<div class="modal-body">
				<form id="modal-form" onsubmit="update(); return false;">
					<div>
						<label>Username: </label>
						<label><?=$user['username'];?></label>
						<br>
						<label>Usergroup: </label>
						<label><?=$user['userGroup'] == 1 ? 'Student' : 'Teacher';?></label>
					</div>
					<div class="form-group">
						<label>Name: </label>
						<input name="name" type="input" class="form-control" id="modal-name" />
					</div>
					<div class="form-group">
						<label>Gender: </label>
						<select name="gender" class="form-control" id="modal-gender">
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div>
					<div class="form-group">
						<label>Grade: </label>
						<input type="input" name="grade" class="form-control" id="modal-grade" />
					</div>
					</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-primary" value="Save Changes" />
				</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>
<?php
// 这里暂时只用 empty 判断
if (empty($user['info'])) {
?>
<script>
openModal();
alert('Please complete your info.');
</script>
<?php
}
?>
<script>
updateInfo();
</script>
</body>
</html>
