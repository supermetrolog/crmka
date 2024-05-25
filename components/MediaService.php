<?php

namespace app\components;

use yii\base\Component;
use yii\web\UploadedFile;

class MediaService extends Component
{
	public string $diskPath = '';
	public string $webPath = '';

	public function __construct($config = [])
	{
		$config['diskPath'] = rtrim($config['diskPath'], '\\/');
		$config['webPath'] = rtrim($config['webPath'], '\\/');

		parent::__construct($config);
	}

	public function put(string $filename, UploadedFile $uploadedFile): void
	{
		$uploadedFile->saveAs($this->diskPath($filename));
	}

	public function delete(string $filename): void
	{
		$path = $this->diskPath($filename);

		if (! file_exists($path)) {
			return;
		}

		unlink($path);
	}

	public function webPath(string $filename): string
	{
		return $this->webPath . '/' . trim($filename, '\\/');
	}

	protected function diskPath(string $filename): string
	{
		return $this->diskPath . '/' . trim($filename, '\\/');
	}
}
