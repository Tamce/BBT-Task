<?php
require __DIR__ . '/../vendor/autoload.php';

use ElfStack\Router;
use ElfStack\Renderer;

Renderer::path(__DIR__ . '/../src/Views/');

Router::route('/install', 'Tamce\BBT\Installer@install');
Router::route('/', 'Tamce\BBT\Controllers\Common@index');

// Any other request return 404
Router::route('*', function () {
	Renderer::render('errors/404', [], 404);
});
