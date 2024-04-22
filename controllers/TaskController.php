<?php

namespace app\controllers;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\forms\Task\TaskForm;
use app\models\search\TaskSearch;
use app\models\Task;
use app\resources\TaskResource;
use app\usecases\Task\CreateTaskService;
use app\usecases\Task\TaskService;
use Exception;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends AppController
{
	private TaskService       $service;
	private CreateTaskService $createTaskService;

	public function __construct(
		$id,
		$module,
		TaskService $service,
		CreateTaskService $createTaskService,
		array $config = []
	)
	{
		$this->service           = $service;
		$this->createTaskService = $createTaskService;

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
	 * @return TaskResource
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws ErrorException
	 * @throws Exception
	 */
	public function actionCreate(): TaskResource
	{
		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$model = $this->createTaskService->create($form->getDto());

		return new TaskResource($model);
	}

	/**
	 * @return array
	 * @throws ErrorException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreateForUsers(): array
	{
		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_CREATE_FOR_USERS);

		$form->load($this->request->post());

		$form->created_by_id   = $this->user->id;
		$form->created_by_type = $this->user->identity::getMorphClass();

		$form->validateOrThrow();

		$models = $this->createTaskService->createForUsers($form->getDto());

		return TaskResource::collection($models);
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
	 * @throws ValidateException
	 * @throws Exception
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
