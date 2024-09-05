<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\TaskObserver\TaskObserverForm;
use app\models\search\TaskObserverSearch;
use app\models\TaskObserver;
use app\resources\Task\TaskObserverResource;
use app\usecases\TaskObserver\TaskObserverService;
use Exception;
use Throwable;
use yii\data\ActiveDataProvider;

class TaskObserverController extends AppController
{
	private TaskObserverService $service;


	public function __construct(
		$id,
		$module,
		TaskObserverService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new TaskObserverSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return TaskObserverResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param int $id
	 *
	 * @return TaskObserverResource
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): TaskObserverResource
	{
		return new TaskObserverResource($this->findModel($id));
	}


	/**
	 * @throws ValidateException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function actionCreate(): TaskObserverResource
	{
		$form = new TaskObserverForm();
		$form->load($this->request->post());

		$form->created_by_id = $this->user->id;

		$form->validateOrThrow();

		$observer = $this->service->create($form->getDto());

		return new TaskObserverResource($observer);
	}

	/**
	 * @throws Throwable
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModel($id));

		return new SuccessResponse();
	}

	/**
	 * @param int $id
	 *
	 * @return TaskObserver|null
	 * @throws ModelNotFoundException
	 */
	protected function findModel(int $id): ?TaskObserver
	{
		if (($model = TaskObserver::findOne($id)) !== null) {
			return $model;
		}

		throw new ModelNotFoundException('TaskObserver not found');
	}
}
