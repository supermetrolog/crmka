<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\TaskTag\TaskTagForm;
use app\models\search\TaskTagSearch;
use app\models\TaskTag;
use app\resources\TaskTagResource;
use app\usecases\TaskTag\TaskTagService;
use Exception;
use Throwable;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskTagController extends AppController
{
	private TaskTagService $service;

	public function __construct(
		$id,
		$module,
		TaskTagService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new TaskTagSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return TaskTagResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): TaskTagResource
	{
		return new TaskTagResource($this->findModel($id));
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionCreate(): TaskTagResource
	{
		$form = new TaskTagForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new TaskTagResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 */
	public function actionUpdate(int $id): TaskTagResource
	{
		$model = $this->findModel($id);

		$form = new TaskTagForm();

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new TaskTagResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModel($id));

		return new SuccessResponse();
	}


	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?TaskTag
	{
		if (($model = TaskTag::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}
}
