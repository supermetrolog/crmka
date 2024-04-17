<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\forms\Task\TaskForm;
use app\models\search\TaskSearch;
use app\models\Task;
use app\resources\TaskResource;
use app\usecases\TaskService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends AppController
{
	private TaskService $service;

	public function __construct($id, $module, TaskService $service, array $config = [])
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new TaskSearch();

		return $searchModel->search(Yii::$app->request->queryParams);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): TaskResource
	{
		return new TaskResource($this->findModel($id));
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): TaskResource
	{
		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new TaskResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): TaskResource
	{
		$model = $this->findModel($id);

		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new TaskResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): void
	{
		$this->service->delete($this->findModel($id));
	}


	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?Task
	{
		$model = Task::find()
		             ->byId($id)
		             ->byMorph($this->user->id, $this->user->identity::getMorphClass())
		             ->one();

		if ($model) {
			return $model;
		}

		throw new NotFoundHttpException('The requested model does not exist.');
	}
}
