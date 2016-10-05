<div class="container-fluid">
<div class="jumbotron">
	<h1>Hello</h1>
	<h2>Welcome to Student Manage System!</h2>
	<p>To start, pick an action on the navbar.</p>
	<span id="home-btn">
	<a class="btn btn-primary btn-lg" href="#login">Login</a>
	<a class="btn btn-default btn-lg" href="#register">Register</a>
	</span>
	<p id="home-user" style="display: none;">You have signed in as <a id="home-user-username" href="#profile"></a></p>
</div>
</div>
<script>
window._sms.stageTrigger.push(function (stage) {
	if (stage == "home") {
		if (window._sms.login) {
			$("#home-btn").css("display", "none");
			$("#home-user").css("display", "block");
			$("#home-user-username").text(window._sms.user.username);
		} else {
			$("#home-btn").css("display", "block");
			$("#home-user").css("display", "none");
		}
	}
});
</script>
