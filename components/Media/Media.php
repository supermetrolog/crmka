<?php

declare(strict_types=1);

namespace app\components\Media;

use app\components\PathBuilder\PathBuilderFactory;
use app\helpers\StringHelper;
use Yii;
use yii\base\Component;
use yii\web\UploadedFile;

class Media extends Component
{
	private PathBuilderFactory $pathBuilderFactory;

	public string  $diskPath;
	public string  $baseFolder;
	private string $baseUrl;

	public function __construct(PathBuilderFactory $pathBuilderFactory, $baseUrl = null, $config = [])
	{
		parent::__construct($config);

		$this->pathBuilderFactory = $pathBuilderFactory;
		$this->diskPath           = $this->pathBuilderFactory->create()->addPart($this->diskPath)->build()->getRel();

		if (!$baseUrl) {
			$this->baseUrl = Yii::$app->request->hostInfo . $this->baseFolder;
		} else {
			$this->baseUrl = $baseUrl;
		}
	}

	public function getUrl(string $filePath): string
	{
		return StringHelper::join(StringHelper::SYMBOL_SLASH, $this->baseUrl, $filePath);
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
