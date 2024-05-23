<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Task\TaskChangeStatusForm;
use app\models\forms\Task\TaskForm;
use app\models\search\TaskSearch;
use app\models\Task;
use app\repositories\TaskRepository;
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
	private TaskRepository    $repository;


	public function __construct(
		$id,
		$module,
		TaskService $service,
		CreateTaskService $createTaskService,
		TaskRepository $repository,
		array $config = []
	)
	{
		$this->service           = $service;
		$this->createTaskService = $createTaskService;
		$this->repository        = $repository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel  = new TaskSearch();
		$dataProvider = $searchModel->search($this->request->get());

		return TaskResource::fromDataProvider($dataProvider);
	}

	/**
	 * @param int $id
	 *
	 * @return TaskResource
	 * @throws ModelNotFoundException
	 */
	public function actionView(int $id): TaskResource
	{
		return new TaskResource($this->findModelByIdAndCreatedBy($id));
	}

	public function actionStatistic(): array
	{
		return $this->repository->getStatusStatisticByUserId($this->request->get());
	}

	/**
	 * @return TaskResource
	 * @throws SaveModelException
	 * @throws ValidateException
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
	 * @param int $id
	 *
	 * @return TaskResource
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): TaskResource
	{
		$model = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new TaskResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return SuccessResponse
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionChangeStatus(int $id): SuccessResponse
	{
		$task = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new TaskChangeStatusForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->changeStatus($task, $form->status);

		return new SuccessResponse();
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$this->service->delete($this->findModelByIdAndCreatedBy($id));

		return new SuccessResponse();
	}

	/**
	 * @param int $id
	 *
	 * @return Task
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedBy(int $id): Task
	{
		return $this->repository->findModelByIdAndCreatedBy($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @param int $id
	 *
	 * @return Task
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedByOrUserId(int $id): Task
	{
		return $this->repository->findModelByIdAndCreatedByOrUserId($id, $this->user->id, $this->user->identity::getMorphClass());
	}
}
