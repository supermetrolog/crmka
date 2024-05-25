<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class MediaService extends Component
{
	public string $path = '';

	public function __construct($config = [])
	{
		$config['path'] = rtrim($config['path'], '\\/');

		parent::__construct($config);
	}

	public function put(string $filename, UploadedFile $uploadedFile): void
	{
		$uploadedFile->saveAs($this->path($filename));
	}

	public function delete(string $filename): void
	{
		unlink($this->path($filename));
	}

	public function path(string $filename): string
	{
		return $this->path . '/' . trim($filename, '\\/');
	}
}
