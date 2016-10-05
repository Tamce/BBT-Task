<?php
header('Content-Type: application/json');
?>
{
	"documentations": null,
	"authorization_url": "/api/authorization",
	"current_user_url": "/api/user",
	"all_users_url": "/api/users",
	"user_url": "/api/user/{username}",
	"all_class_url": "/api/class",
	"class_url": "/api/class/{classname}",
	"users_in_class_url": "/api/class/{classname}/{type}"
}
