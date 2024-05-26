<?php

namespace app\components;

use yii\base\Component;

class MediaPathBuilder extends Component
{
	public string $diskPath = '';
	public string $webPath = '';

	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->diskPath = rtrim($this->diskPath, '\\/') . '/';
		$this->webPath = rtrim($this->webPath, '\\/') . '/';
	}

	public function disk(string $path, string $name, string $extension): string
	{
		return $this->build($this->diskPath, $path, $name, $extension);
	}

	public function web(string $path, string $name, string $extension): string
	{
		return $this->build($this->webPath, $path, $name, $extension);
	}

	private function build(string $base, string $path, string $name, string $extension): string
	{
		return $base . trim($path, '\\/') . '/' . $name . '.' . $extension;
	}
}
