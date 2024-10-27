<?php

declare(strict_types=1);

namespace app\components\Media;

use app\components\PathBuilder\PathBuilderFactory;
use yii\base\Component;
use yii\web\UploadedFile;

class Media extends Component
{
	private PathBuilderFactory $pathBuilderFactory;

	private string $diskPath;
	private string $baseUrl;

	public function __construct(PathBuilderFactory $pathBuilderFactory, string $baseUrl, string $diskPath, $config = [])
	{
		parent::__construct($config);

		$this->pathBuilderFactory = $pathBuilderFactory;
		$this->baseUrl            = $baseUrl;

		$this->diskPath = $this->pathBuilderFactory
			->create()
			->addPart($diskPath)
			->build()
			->getRel();
	}

	public function getUrl(string $filePath): string
	{
		return $this->pathBuilderFactory
			->create()
			->addPart($this->baseUrl)
			->addPart($filePath)
			->build()
			->getRel();
	}

	/**
	 * @throws SaveMediaErrorException
	 */
	public function put(string $path, UploadedFile $uploadedFile): void
	{
		$path = $this->pathBuilderFactory
			->create()
			->addPart($this->diskPath)
			->addPart($path)
			->build()
			->getAbs();

		if (!$uploadedFile->saveAs($path)) {
			throw new SaveMediaErrorException(sprintf('Save media to path "%s" error', $path));
		}
	}

	public function delete(string $path): void
	{
		$path = $this->pathBuilderFactory
			->create()
			->addPart($this->diskPath)
			->addPart($path)
			->build();

		$path->unlink();
	}
}
