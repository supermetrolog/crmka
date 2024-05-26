<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class Media extends Component
{
	private MediaPathBuilder $pathBuilder;

	public function __construct(MediaPathBuilder $pathBuilder, $config = [])
	{
		$this->pathBuilder = $pathBuilder;

		parent::__construct($config);
	}

	public function put(string $path, string $name, string $extension, UploadedFile $uploadedFile): void
	{
		$uploadedFile->saveAs($this->pathBuilder->disk($path, $name, $extension));
	}

	public function delete(string $path, string $name, string $extension): void
	{
		$path = $this->pathBuilder->disk($path, $name, $extension);

		if (! file_exists($path)) {
			return;
		}

		unlink($path);
	}

	public function pathBuilder(): MediaPathBuilder
	{
		return $this->pathBuilder;
	}
}
