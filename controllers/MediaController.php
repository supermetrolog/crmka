<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Media;
use app\models\search\MediaSearch;
use app\repositories\MediaRepository;
use app\resources\Media\MediaResource;
use app\usecases\Media\CreateMediaService;
use app\usecases\Media\MediaService;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class MediaController extends AppController
{
	private MediaService       $service;
	private CreateMediaService $createMediaService;
	private MediaRepository    $repository;


	public function __construct(
		$id,
		$module,
		MediaService $service,
		CreateMediaService $createMediaService,
		MediaRepository $repository,
		array $config = []
	)
	{
		$this->service            = $service;
		$this->createMediaService = $createMediaService;
		$this->repository         = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new MediaSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return MediaResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param int $id
	 *
	 * @return MediaResource
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): MediaResource
	{
		return new MediaResource($this->findModelByIdAndModel($id));
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModelByIdAndModel($id));

		return new SuccessResponse();
	}


	/**
	 * @param int $id
	 *
	 * @return Media
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndModel(int $id): Media
	{
		return $this->repository->findModelByIdAndModel($id, $this->user->id, $this->user->identity::getMorphClass());
	}
}
