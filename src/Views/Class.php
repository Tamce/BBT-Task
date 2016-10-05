<div>
	<div class="page-header">
		<h1>Class's Infomation</h1>
	</div>
	<div class="col-md-6 col-md-offset-3">
		<div class="alert alert-info">
			<p>Pick a class you are in to see the members</p>
		</div>
		<div class="alert alert-success" id="class-admin-only" style="display: none;">
			<p>As an Admin, you can <a href="/api/export/all" class="alert-link" target="_blank">Export All data as XLS</a></p>
		</div>
		<h3>Class List</h3>
		<div id="class-list" class="list-group"></div>
		<div id="class-member" style="display: none;">
			<hr>
			<div class="form-group">
				<div class="col-md-3"><label class="control-label"><input id="class-show-stu" type="checkbox" checked />&nbsp;Show Students</label></div>
				<div class="col-md-3"><label class="control-label"><input id="class-show-tea" type="checkbox" checked />&nbsp;Show Teachers</label></div>
				<div class="col-md-3"><label class="control-label"><input id="class-count" type="number" value="10" style="width: 50px;" /> each page</label></div>
				<div class="col-md-3"><button class="btn btn-primary" onclick="classRefresh();">Refresh</button></div>
			</div>
			<hr>
			<table class="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Gender</th>
						<th>User Group</th>
						<th>Class</th>
					</tr>
				</thead>
				<tbody id="class-member-data">
				</tbody>
			</table>
			<div style="width: 50%; margin-left: 25%;">
				<div class="alert alert-info"><span id="class-total-count">0</span> record(s) in total</div>
				<div class="btn-group" id="class-page" style="text-align: center;">
					<button class="btn btn-default" id="class-page-pre" onclick="lastPage();">&lt; Previous</button>
					<button class="btn btn-default"><span id="class-page-current"></span> / <span id="class-page-total"></span></button>
					<button class="btn btn-default" id="class-page-next" onclick="nextPage();">Next &gt;</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function chooseClass(e)
{
	$("#class-list a").removeClass("active");
	$(e).addClass("active");
	$("#class-member").fadeIn();
}

function classRefresh()
{
	$("#class-page").attr("count", $("#class-count").val());
	fetchData(0);
}

function lastPage()
{
	fetchData($("#class-page").attr("count") * ($("#class-page").attr("page-current") - 2));
}

function nextPage()
{
	fetchData($("#class-page").attr("count") * ($("#class-page").attr("page-current")));
}

function fetchData(begin)
{
	var clas = $("#class-list").children(".active").attr("data-class");
	$("#class-member-data").html("");
	var type = "";
	if ($("#class-show-stu").prop("checked")) {
		type = "student";
		if ($("#class-show-tea").prop("checked")) {
			type = "all";
		}
	} else {
		if ($("#class-show-tea").prop("checked")) {
			type = "teacher";
		} else {
			return;
		}
	}
	$.ajax("/api/class/"+clas+"/"+type+"?begin="+begin+"&count="+$("#class-page").attr("count"), {
		success: function (data) {
			$("#class-total-count").text(data.totalCount);
			pageTotal = Math.ceil(data.totalCount / $("#class-page").attr("count"));
			pageCurrent = Math.floor(begin / $("#class-page").attr("count")) + 1;
			$("#class-page").attr("page-total", pageTotal);
			$("#class-page-current").text(pageCurrent);
			$("#class-page-total").text(pageTotal);
			$("#class-page").attr("page-current", pageCurrent);
			if (pageCurrent == 1) {
				$("#class-page-pre").attr("disabled", "disabled");
			} else {
				$("#class-page-pre").attr("disabled", false);
			}
			if (pageTotal == pageCurrent) {
				$("#class-page-next").attr("disabled", "disabled");
			} else {
				$("#class-page-next").attr("disabled", false);
			}

			$.each(data.data, function (i, v) {
				_group = v.userGroup == 1 ? 'Student' : v.userGroup == 2 ? 'Teacher' : 'Admin';
				_clas = "";
				if (v.userGroup == 2) {
					_clas = v.classname.join("<br>");
				} else {
					_clas = v.classname;
				}
				$("#class-member-data").append("<tr><td>"+v.name+"</td><td>"+v.gender+"</td><td>"+_group+"</td><td>"+_clas+"</td></tr>");
			});
		},
		error: function (xhr) {
			alert("An error occurred!\n" + xhr.status + " " + xhr.statusText);
		}
	});
}

window._sms.stageTrigger.push(function (stage) {
	if (stage == "class") {
		if (window._sms.login != true) {
			alert("Please Login First!");
			window.location.hash = "#login";
			return;
		}

		if (window._sms.user.userGroup == 3) {
			$("#class-admin-only").css("display", "block");
		} else {
			$("#class-admin-only").css("display", "none");
		}

		$("#class-list").html("");
		if (window._sms.user.userGroup == 2) {
			$.each(window._sms.user.classname, function (i, v) {
				$("#class-list").append('<a class="list-group-item" data-class="'+v+'">'+v+'</a>');
			});
		} else if (window._sms.user.userGroup == 3) {
			$.ajax("/api/class", {
				method: "GET",
				success: function (data) {
					$.each(data.data, function (i, v) {
						$("#class-list").append('<a class="list-group-item" data-class="'+v.classname+'">'+v.classname+'</a>');
					});
					$("#class-list a").css("cursor", "pointer").click(function (e) {
						chooseClass(e.target);
					});
				}
			})
		} else {
			$("#class-list").append('<a class="list-group-item" data-class="'+window._sms.user.classname+'">'+window._sms.user.classname+'</a>');
		}
		$("#class-list a").css("cursor", "pointer").click(function (e) {
			chooseClass(e.target);
		});
		$("#class-list a").removeClass("active");
		$("#class-member").css("display", "none");
		$("#class-member-data").html("");
		$("#class-page-pre").attr("disabled", "disabled");
		$("#class-page-next").attr("disabled", "disabled");
	}
});
</script>
