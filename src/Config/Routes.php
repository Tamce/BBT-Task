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
Router::get('/api/verify_update/{vid}', 'Tamce\BBT\Controllers\User@verifyUpdate');

// 下方待整理
Router::post('/api/user', 'Tamce\BBT\Controllers\Api\User@create');
Router::patch('/api/user/{username}', 'Tamce\BBT\Controllers\Api\User@patch');
Router::get('/api/user/{username}', 'Tamce\BBT\Controllers\Api\User@profile');
Router::delete('/api/user/{username}', 'Tamce\BBT\Controllers\Api\User@delete');

Router::post('/api/class', 'Tamce\BBT\Controllers\Api\Class@create');
Router::get('/api/class/{name}', 'Tamce\BBT\Controllers\Api\Class@info');
Router::patch('/api/class/{name}', 'Tamce\BBT\Controllers\Api\Class@patch');
Router::delete('/api/class/{name}', 'Tamce\BBT\Controllers\Api\Class@delete');


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
