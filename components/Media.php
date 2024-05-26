<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class Media extends Component
{
	private PathBuilder $pathBuilder;

	public string $diskPath = '';
	public string $webPath = '';

	public function __construct(PathBuilder $pathBuilder, $config = [])
	{
		$this->pathBuilder = $pathBuilder;

		parent::__construct($config);

		$this->diskPath = rtrim($this->diskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		$this->webPath = rtrim($this->webPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	public function put(string $path, UploadedFile $uploadedFile): void
	{
		$uploadedFile->saveAs($this->pathBuilder->join($this->diskPath, $path));
	}

	public function delete(string $path): void
	{
		$path = $this->pathBuilder->join($this->diskPath, $path);

		if (! file_exists($path)) {
			return;
		}

		unlink($path);
	}

	public function webPath(string $path): string
	{
		return $this->pathBuilder->join($this->webPath, $path);
	}

	public function pathBuilder(): PathBuilder
	{
		return $this->pathBuilder;
	}
}
