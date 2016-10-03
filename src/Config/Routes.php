<?php
use ElfStack\Router;
use Tamce\BBT\Core\Helper;
use ElfStack\Renderer;

/**
 * -----------------------------------------------------------
 *                       初始化数据库
 * -----------------------------------------------------------
 */
Router::route('/installer/install', 'Tamce\BBT\Installer@install');
Router::route('/installer/uninstall', 'Tamce\BBT\Installer@uninstall');
Router::route('/installer/reinstall', 'Tamce\BBT\Installer@reinstall');


// 简单的测试 [随时变更]
Router::route('/test/user', 'Tamce\BBT\Controllers\Tests@testUser');

/**
 * -----------------------------------------------------------
 *                         Api 接口
 * -----------------------------------------------------------
 */
Router::route('/api', function () {
	Renderer::render('Api');
});
Router::post('/api/authorization', 'Tamce\BBT\Controllers\User@authorize');
Router::get('/api/users', 'Tamce\BBT\Controllers\User@listUser');
Router::post('/api/users', 'Tamce\BBT\Controllers\User@create');
Router::route('/api/user', 'Tamce\BBT\Controllers\User@current');
Router::get('/api/user/{username}', 'Tamce\BBT\Controllers\User@info');
Router::get('/api/verify_update/{username}', 'Tamce\BBT\Controllers\User@verifyUpdate');

// 待编写
Router::get('/api/user/avatar', 'Tamce\BBT\Controllers\User@avatar');
Router::post('/api/user/avatar', 'Tamce\BBT\Controllers\User@uploadAvatar');
Router::get('/api/user/{username}/avatar', 'Tamce\BBT\Controllers\User@avatar');
Router::post('/api/class', 'Tamce\BBT\Controllers\CClass@create');
Router::get('/api/class/{classname}', 'Tamce\BBT\Controllers\CClass@show');
Router::get('/api/class', 'Tamce\BBT\Controllers\CClass@listClass');
Router::patch('/api/class/{classname}', 'Tamce\BBT\Controllers\CClass@patch');
Router::get('/api/class/{classname}/{type}', 'Tamce\BBT\Controllers\CClass@member');


/**
 * -----------------------------------------------------------
 *                         临时的主页导航
 * -----------------------------------------------------------
 */
Router::route('/', function () {
	echo <<<EOD
<a href="/installer/install">Set up database</a><br>
<a href="/installer/uninstall">Clean database</a><br>
<a href="/installer/reinstall">Clean and Re-SetUp database</a><br>
<hr>
<a href="/login">Login</a><br>
<a href="/register">Register</a><br>
<a href="/logout">Logout</a><br>
<a href="/profile">Profile</a>
EOD;
});

// Any other request return 404
Router::route('*', function () {
	Helper::abort(404);
});
