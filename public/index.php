<?php
require __DIR__ . '/../vendor/autoload.php';

use ElfStack\Router;
use ElfStack\Renderer;

Renderer::path(__DIR__ . '/../src/Views/');

Router::route('/installer/install', 'Tamce\BBT\Installer@install');
Router::route('/installer/uninstall', 'Tamce\BBT\Installer@uninstall');
Router::route('/installer/reinstall', 'Tamce\BBT\Installer@reinstall');

Router::route('/test/pdo', 'Tamce\BBT\Controllers\Tests@testPdo');

Router::route('/add', 'Tamce\BBT\Controllers\Common@add');

Router::route('/', 'Tamce\BBT\Controllers\Common@index');

// Any other request return 404
Router::route('*', function () {
	Renderer::render('errors/404', [], 404);
});
