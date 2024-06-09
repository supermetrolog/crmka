<?php

declare(strict_types=1);

namespace app\components\Media;

use app\components\PathBuilder\PathBuilderFactory;
use yii\base\Component;
use yii\web\UploadedFile;

class Media extends Component
{
	private PathBuilderFactory $pathBuilderFactory;

	public string $diskPath;

	public function __construct(PathBuilderFactory $pathBuilderFactory, $config = [])
	{
		parent::__construct($config);

		$this->pathBuilderFactory = $pathBuilderFactory;
		$this->diskPath           = $this->pathBuilderFactory->create()->addPart($this->diskPath)->build()->getRel();
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
