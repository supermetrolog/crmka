<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\archiver\ArchiverFactory;
use app\components\archiver\File;
use SplFileInfo;
use Yii;
use yii\base\ErrorException;
use yii\web\BadRequestHttpException;
use yii\web\RangeNotSatisfiableHttpException;

class ArchiverController extends AppController
{
	protected array $exceptAuthActions = ['download'];

	private ArchiverFactory $archiverFactory;

	public function __construct($id, $module, ArchiverFactory $archiverFactory, array $config = [])
	{
		$this->archiverFactory = $archiverFactory;
		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws BadRequestHttpException
	 * @throws ErrorException
	 * @throws RangeNotSatisfiableHttpException
	 */
	public function actionDownload(): void
	{
		$files = $this->request->get('files');

		if (!is_array($files)) {
			throw new BadRequestHttpException('Query param "files" must be array');
		}

		// Важная штука. Если не валидировать ссылку можно передать просто путь к файлам исходиников
		array_walk($files, function ($file) {
			if (!filter_var($file, FILTER_VALIDATE_URL)) {
				throw new BadRequestHttpException('File must be url');
			}
		});

		$zipFilename = hash('md5', implode('_', $files)) . '.zip';

		$filename = Yii::getAlias('@runtime/' . $zipFilename);

		$archiver = $this->archiverFactory->create($filename);

		foreach ($files as $key => $file) {
			$fileInfo = new SplFileInfo($file);
			$ext = $fileInfo->getExtension();

			$archiverFile = new File($key . '.' . $ext, file_get_contents($file));

			$archiver->add($archiverFile);
		}

		$archiver->save();

		$archiveContent = file_get_contents($filename);

		$this->response->sendContentAsFile($archiveContent, $zipFilename);

		unlink($filename);
	}
}