<?php
namespace ElfStack;

use Exception;

class Renderer
{
	static $path;
	static public function render($viewFile, $data = [], $statusCode = null)
	{
		try {
			$page = new Renderer\Page(self::$path.$viewFile, $data);
		} catch (Exception $e) {
			throw $e;
		}

		if (!empty($statusCode)) {
			http_response_code($statusCode);
		}
		$page->render();
	}

	static public function path($path)
	{
		return self::$path = $path;
	}
}

namespace ElfStack\Renderer;
class Page
{
	protected $file;
	protected $data;
	public function __construct($viewFile, array $data)
	{
		$this->file = $viewFile . '.php';
		if (!file_exists($this->file)) {
			throw new Exception("Failed to load file: `$viewFile`, file not exist!");
		}
		$this->data = $data;
	}

	public function render()
	{
		extract($this->data);
		include($this->file);
	}

	// ------- Helper Functions below are for view files

	protected function css($path, $echo = true)
	{
		$buf = '<link href="'.$path.'" rel="stylesheet" />';
		if ($echo) {
			echo $buf;
		}
		return $buf;
	}

	protected function js($path, $echo = true)
	{
		$buf = '<script src="'.$path.'"></script>';
		if ($echo) {
			echo $buf;
		}
		return $buf;
	}
}
