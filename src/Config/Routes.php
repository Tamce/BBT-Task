<?php
use ElfStack\Router;
use Tamce\BBT\Core\Helper;
use ElfStack\Renderer;

Router::route('/installer/install', 'Tamce\BBT\Installer@install');
Router::route('/installer/uninstall', 'Tamce\BBT\Installer@uninstall');
Router::route('/installer/reinstall', 'Tamce\BBT\Installer@reinstall');

Router::route('/test/user', 'Tamce\BBT\Controllers\Tests@testUser');

Router::route('/register', function () {
	Renderer::render('Register');
});
Router::route('/login', function () {
	Renderer::render('Login');
});
Router::route('/profile', function () {
	Renderer::render('Profile', $_SESSION);
});
Router::route('/logout', function () {
	unset($_SESSION['user']);
	unset($_SESSION['login']);
	echo '<script>window.location.href = "/login";</script>';
});

Router::post('/api/login', 'Tamce\BBT\Controllers\Api\User@login');
Router::post('/api/rigister', 'Tamce\BBT\Controllers\Api\User@register');
Router::post('/api/update', 'Tamce\BBT\Controllers\Api\User@update');

// Router::route('/', 'Tamce\BBT\Controllers\Common@index');

// Any other request return 404
Router::route('*', function () {
	Helper::abort(404);
});
