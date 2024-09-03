<?php

namespace app\controllers;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Task\TaskChangeStatusForm;
use app\models\forms\Task\TaskCommentForm;
use app\models\forms\Task\TaskForm;
use app\models\search\TaskSearch;
use app\models\Task;
use app\repositories\TaskCommentRepository;
use app\repositories\TaskObserverRepository;
use app\repositories\TaskRepository;
use app\resources\Task\TaskCommentResource;
use app\resources\Task\TaskResource;
use app\resources\Task\TaskWithRelationResource;
use app\usecases\Task\ChangeTaskStatusService;
use app\usecases\Task\CreateTaskCommentService;
use app\usecases\Task\CreateTaskService;
use app\usecases\Task\TaskService;
use app\usecases\TaskObserver\TaskObserverService;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class TaskController extends AppController
{
	private TaskService              $service;
	private CreateTaskService        $createTaskService;
	private TaskRepository           $repository;
	private CreateTaskCommentService $createTaskCommentService;
	private TaskCommentRepository    $taskCommentRepository;
	private TaskObserverService      $taskObserverService;
	private TaskObserverRepository   $taskObserverRepository;
	private ChangeTaskStatusService  $changeTaskStatusService;

	public function __construct(
		$id,
		$module,
		TaskService $service,
		CreateTaskService $createTaskService,
		CreateTaskCommentService $createTaskCommentService,
		TaskCommentRepository $taskCommentRepository,
		TaskRepository $repository,
		TaskObserverRepository $taskObserverRepository,
		TaskObserverService $taskObserverService,
		ChangeTaskStatusService $changeTaskStatusService,
		array $config = []
	)
	{
		$this->service                  = $service;
		$this->createTaskService        = $createTaskService;
		$this->createTaskCommentService = $createTaskCommentService;
		$this->repository               = $repository;
		$this->taskCommentRepository    = $taskCommentRepository;
		$this->changeTaskStatusService  = $changeTaskStatusService;
		$this->taskObserverService      = $taskObserverService;
		$this->taskObserverRepository   = $taskObserverRepository;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
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
	 * @return TaskWithRelationResource
	 */
	public function actionView(int $id): TaskWithRelationResource
	{
		return new TaskWithRelationResource($this->findModelById($id));
	}

	public function actionStatistic(): array
	{
		return $this->repository->getStatusStatisticByUserId($this->request->get('user_id'));
	}

	/**
	 * @throws ErrorException
	 */
	public function actionCounts()
	{
		$user_id       = $this->request->get('user_id');
		$created_by_id = $this->request->get('created_by_id');
		$observer_id   = $this->request->get('observer_id');

		if (empty($observer_id)) {
			return $this->repository->getCountsByUserIdOrCreatedById($user_id, $created_by_id);
		} else {
			return $this->repository->getCountsByObserverIdAndByUserIdOrCreatedById($user_id, $created_by_id, $observer_id);
		}
	}

	/**
	 * @throws ErrorException
	 */
	public function actionRelations()
	{
		$user_id = $this->request->get('user_id');

		return $this->repository->getRelationsStatisticByUserId($user_id);
	}

	/**
	 * @return TaskResource
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
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
	 * @throws Exception
	 */
	public function actionUpdate(int $id): TaskResource
	{
		$model = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->created_by_id = $this->user->id;
		$form->validateOrThrow();

		$model = $this->service->update($model, $form->getDto());

		return new TaskResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function actionChangeStatus(int $id): array
	{
		$task = $this->findModelByIdAndCreatedByOrUserId($id);

		$form = new TaskChangeStatusForm();
		$form->load($this->request->post());

		$form->changed_by_id = $this->user->id;

		$form->validateOrThrow();

		$this->changeTaskStatusService->changeStatus($task, $form->getDto());

		return TaskWithRelationResource::tryMake($task)->toArray();
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

	public function actionComments(int $id): array
	{
		$models = $this->taskCommentRepository->findAllByTaskId($id);

		return TaskCommentResource::collection($models);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionCreateComment(int $id): TaskCommentResource
	{
		$form = new TaskCommentForm();

		$form->setScenario(TaskCommentForm::SCENARIO_CREATE);

		$form->load($this->request->post());
		$form->task_id       = $id;
		$form->created_by_id = $this->user->id;

		$form->validateOrThrow();

		$model = $this->createTaskCommentService->create($form->getDto());

		return new TaskCommentResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionRead(int $id): SuccessResponse
	{
		$task     = $this->findModelById($id);
		$observer = $this->taskObserverRepository->findOneByTaskIdAndUserId($task->id, $this->user->id);

		$this->taskObserverService->observe($observer);

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

	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelById(int $id): Task
	{
		return $this->repository->findModelById($id);
	}
}
