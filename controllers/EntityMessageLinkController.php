<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\search\EntityMessageLinkSearch;
use app\repositories\EntityMessageLinkRepository;
use app\resources\EntityMessageLink\EntityMessageLinkResource;
use app\usecases\EntityMessageLink\EntityMessageLinkService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class EntityMessageLinkController extends AppController
{
	private EntityMessageLinkService    $service;
	private EntityMessageLinkRepository $repository;

	public function __construct(
		$id,
		$module,
		EntityMessageLinkService $service,
		EntityMessageLinkRepository $repository,
		array $config = []
	)
	{
		$this->service    = $service;
		$this->repository = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new EntityMessageLinkSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return EntityMessageLinkResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function actionDelete($id): SuccessResponse
	{
		$comment = $this->repository->findOneOrThrow($id);

		$this->service->delete($comment);

		return new SuccessResponse('Сообщение откреплено');
	}
}