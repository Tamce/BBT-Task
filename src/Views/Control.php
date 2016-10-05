<?php
use Tamce\BBT\Core\Helper;
Helper::ensureLogin();
Helper::loadConstants();
if ($_SESSION['user']['userGroup'] != UserGroup::Admin) {
	header('Content-Type: application/json');
	http_response_code(403);
	echo json_encode(['status' => 'error', '403 Forbidden!']);
	die();
}
?>
<div class="page-header">
	<h1>Control Pannel<small> &nbsp; Admin Only</small></h1>
</div>
<div class="col-md-6 col-md-offset-3">
	<h2>Class List</h2>
	<hr>
	<form onsubmit="addClass(); return false;">
	<div class="input-group">
		<input type="text" class="form-control" id="ctrl-classname">
		<span class="input-group-btn">
		<input class="btn btn-primary" type="submit" value="Add a new class" />
		</span>
	</div>
	</form>
	<table class="table table-condensed">
		<thead>
			<tr>
				<th>Id</th>
				<th>Class Name</th>
			</tr>
		</thead>
		<tbody id="ctrl-class-list">
		</tbody>
	</table>
</div>
<script>
function updateClassList(data)
{
	$("#ctrl-class-list").html("");
	$.each(data, function (i, v) {
		$("#ctrl-class-list").append("<tr><td>"+v.id+"</td><td>"+v.classname+"</td></tr>");
	});
}

function addClass()
{
	if ($("#ctrl-classname").val() == "") {
		alert("Please input a name!");
		return;
	}
	$.ajax("/api/class", {
		method: "POST",
		data: "classname="+$("#ctrl-classname").val(),
		success: function (data) {
			if (data.status != "success") {
				alert(data.info);
			} else {
				$("#ctrl-class-list").append("<tr><td>"+data.data.id+"</td><td>"+data.data.classname+"</td></tr>");
				$("#ctrl-classname").val("");
			}
		},
		error: function (xhr) {
			alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
		}
	});
}

window._sms.stageTrigger.push(function (stage) {
	if (stage == "control") {
		$("#ctrl-class-list").html("Loading...");
		$.ajax("/api/class", {
			method: "GET",
			success: function (data) {
				updateClassList(data.data);
			},
			error: function (xhr) {
				alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
			}
		})
	}
});
</script>
