<?php
use Tamce\BBT\Core\Helper;
Helper::ensureLogin();
Helper::loadConstants();
if ($_SESSION['user']['userGroup'] == UserGroup::Student) {
	header('Content-Type: application/json');
	http_response_code(403);
	echo json_encode(['status' => 'error', '403 Forbidden!']);
	die();
}
?>
<div>
<table class="table table-condensed">
		<thead>
			<tr>
				<th>Name</th>
				<th>Gender</th>
				<th>User Group</th>
				<th>Class</th>
				<th>Operation</th>
			</tr>
		</thead>
		<tbody id="verify-user-data">
		</tbody>
	</table>
</div>
<script>
function fillVerify(data)
{
	window._sms.verify = data;
	$.each(data, function (i, v) {
		_group = v.userGroup == 1 ? 'Student' : v.userGroup == 2 ? 'Teacher' : 'Admin';
		_clas = "";
		if (v.userGroup == 2) {
			_clas = v.classname.join("<br>");
		} else {
			_clas = v.classname;
		}
		$("#verify-user-data").append("<tr><td>"+v.name+"</td><td>"+v.gender+"</td><td>"+_group+"</td><td>"+_clas+'</td><td><span class="glyphicon glyphicon-ok" style="cursor:pointer;" onclick="passVerify('+i+');"></span></td></tr>');
	});
}

function passVerify(index)
{
	$.ajax("/api/verify_update/"+window._sms.verify[index].username, {
		method: "GET",
		success: function (data) {
			console.log(data);
			alert(data.info);
			$("#verify-user-data").children()[index].remove();
		},
		error: function (xhr) {
			alert("An error occurred when fetching class info!\n" + xhr.status + " " + xhr.statusText);
		}
	})
}

window._sms.stageTrigger.push(function (stage) {
	if (stage == "verify") {
		$("#verify-user-data").html("");
		$.ajax("/api/verify_update", {
			method: "GET",
			success: function (data) {
				fillVerify(data.data);
			},
			error: function (xhr) {
				alert("An error occurred when fetching class info!\n" + xhr.status + " " + xhr.statusText);
			}
		})
	}
});
</script>
