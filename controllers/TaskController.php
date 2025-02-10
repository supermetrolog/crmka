<?php

namespace app\controllers;

use app\exceptions\services\RelationNotExistsException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\ErrorResponse;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\forms\Media\MediaForm;
use app\models\forms\Task\TaskAssignForm;
use app\models\forms\Task\TaskChangeStatusForm;
use app\models\forms\Task\TaskCommentForm;
use app\models\forms\Task\TaskForm;
use app\models\Media;
use app\models\search\TaskSearch;
use app\models\Task;
use app\repositories\TaskCommentRepository;
use app\repositories\TaskRepository;
use app\resources\Media\MediaResource;
use app\resources\Task\TaskCommentResource;
use app\resources\Task\TaskHistoryViewResource;
use app\resources\Task\TaskRelationStatisticResource;
use app\resources\Task\TaskResource;
use app\resources\Task\TaskStatusStatisticResource;
use app\resources\Task\TaskWithRelationResource;
use app\usecases\Task\AssignTaskService;
use app\usecases\Task\ChangeTaskStatusService;
use app\usecases\Task\CreateTaskCommentService;
use app\usecases\Task\CreateTaskService;
use app\usecases\Task\ObserveTaskService;
use app\usecases\Task\TaskService;
use app\usecases\Task\TaskStateService;
use app\usecases\Task\UpdateTaskService;
use app\usecases\TaskHistory\TaskHistoryService;
use Exception;
use Throwable;
use yii\base\ErrorException;
use yii\base\InvalidCallException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

class TaskController extends AppController
{
	private CreateTaskService        $createTaskService;
	private UpdateTaskService        $updateTaskService;
	private TaskStateService         $taskStateService;
	private ChangeTaskStatusService  $changeTaskStatusService;
	private AssignTaskService        $assignTaskService;
	private TaskRepository           $repository;
	private CreateTaskCommentService $createTaskCommentService;
	private TaskCommentRepository    $taskCommentRepository;
	private ObserveTaskService       $observeTaskService;
	private TaskHistoryService       $taskHistoryService;
	private TaskService              $taskService;

	public function __construct(
		$id,
		$module,
		CreateTaskService $createTaskService,
		UpdateTaskService $updateTaskService,
		TaskStateService $taskStateService,
		ChangeTaskStatusService $changeTaskStatusService,
		AssignTaskService $assignTaskService,
		TaskRepository $repository,
		CreateTaskCommentService $createTaskCommentService,
		TaskCommentRepository $taskCommentRepository,
		ObserveTaskService $observeTaskService,
		TaskHistoryService $taskHistoryService,
		TaskService $taskService,
		array $config = []
	)
	{
		$this->createTaskService       = $createTaskService;
		$this->updateTaskService       = $updateTaskService;
		$this->taskStateService        = $taskStateService;
		$this->changeTaskStatusService = $changeTaskStatusService;
		$this->assignTaskService       = $assignTaskService;
		$this->observeTaskService      = $observeTaskService;

		$this->repository = $repository;

		$this->createTaskCommentService = $createTaskCommentService;
		$this->taskCommentRepository    = $taskCommentRepository;

		$this->taskHistoryService = $taskHistoryService;

		$this->taskService = $taskService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new TaskSearch();

		$searchModel->current_user_id = $this->user->id;

		$dataProvider = $searchModel->search($this->request->get());

		return TaskResource::fromDataProvider($dataProvider);
	}


	/* @throws ModelNotFoundException */
	public function actionView(int $id): TaskWithRelationResource
	{
		if ($this->user->identity->isAdministrator()) {
			$model = $this->repository->findModelByIdWithDeleted($id);
		} else {
			$model = $this->repository->findModelById($id);
		}

		return new TaskWithRelationResource($model);
	}

	public function actionStatistic(): array
	{
		return $this->repository->getStatusStatisticByUserId($this->request->get('user_id'));
	}

	/* @throws ErrorException */
	public function actionCounts(): TaskStatusStatisticResource
	{
		$user_id       = $this->request->get('user_id');
		$created_by_id = $this->request->get('created_by_id');
		$observer_id   = $this->request->get('observer_id');

		$resource = $this->repository->getCountsStatistic($user_id, $created_by_id, $observer_id);

		return new TaskStatusStatisticResource($resource);
	}


	/* @throws ErrorException */
	public function actionRelations(): TaskRelationStatisticResource
	{
		$user_id = $this->request->get('user_id');

		$resource = $this->repository->getRelationsStatisticByUserId($user_id);

		return new TaskRelationStatisticResource($resource);
	}

	/**
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

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_TASK);

		$model = $this->createTaskService->create($form->getDto(), $mediaForm->getDtos());

		return new TaskResource($model);
	}

	/**
	 * @return TaskResource[]
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
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 */
	public function actionUpdate(int $id): TaskWithRelationResource
	{
		$identity = $this->user->identity;

		if ($identity->isAdministrator()) {
			$model = $this->repository->findModelByIdWithDeleted($id);
		} else {
			$model = $this->findModelByIdAndCreatedByOrUserId($id);
		}

		$form = new TaskForm();

		$form->setScenario(TaskForm::SCENARIO_UPDATE);

		$form->load($this->request->post());
		$form->created_by_id = $model->created_by_id;
		$form->validateOrThrow();

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_TASK);

		$model = $this->updateTaskService->update($model, $form->getDto(), $identity, $mediaForm->getDtos());

		return new TaskWithRelationResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionChangeStatus(int $id): TaskWithRelationResource
	{
		$identity = $this->user->identity;

		if ($identity->isAdministrator()) {
			$task = $this->repository->findModelByIdWithDeleted($id);
		} else {
			$task = $this->findModelByIdAndCreatedByOrUserId($id);
		}

		$form = new TaskChangeStatusForm();
		$form->load($this->request->post());

		$form->validateOrThrow();

		$dto            = $form->getDto();
		$dto->changedBy = $this->user->identity;

		$this->changeTaskStatusService->changeStatus($task, $dto);

		return new TaskWithRelationResource($task);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): SuccessResponse
	{
		$identity = $this->user->identity;

		if ($identity->isAdministrator()) {
			$model = $this->repository->findModelById($id);
		} else {
			$model = $this->findModelByIdAndCreatedBy($id);
		}

		$this->taskStateService->delete($model, $this->user->identity);

		return $this->success('Задача успешно удалена');
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

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_TASK_COMMENT);

		$form->files = $mediaForm->files;
		$form->validateOrThrow();

		$model = $this->createTaskCommentService->create($form->getDto(), $mediaForm->getDtos());

		return new TaskCommentResource($model);
	}

	/**
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionRead(int $id): SuccessResponse
	{
		$task = $this->repository->findModelByIdWithDeleted($id);

		$this->observeTaskService->observe($task, $this->user->identity);

		return $this->success();
	}

	/**
	 * @return TaskWithRelationResource|ErrorResponse
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 * @throws Exception
	 * @throws Throwable
	 */
	public function actionAssign(int $id)
	{
		$task = $this->repository->findModelById($id);

		$form = new TaskAssignForm();

		$form->load($this->request->post());
		$form->assignedBy = $this->user->identity;

		$form->validateOrThrow();

		$dto = $form->getDto();

		try {
			$model = $this->assignTaskService->assign($task, $dto);

			return new TaskWithRelationResource($model);
		} catch (ErrorException $e) {
			return $this->errorf('Задача #%s не может быть переназначена', [$id]);
		}
	}

	/**
	 * @return TaskHistoryViewResource[]
	 * @throws ModelNotFoundException
	 */
	public function actionHistory(int $id): array
	{
		$identity = $this->user->identity;

		if ($identity->isAdministrator()) {
			$model = $this->repository->findModelByIdWithDeleted($id);
		} else {
			$model = $this->repository->findModelById($id);
		}

		$histories = $this->taskHistoryService->generateHistory($model);

		return TaskHistoryViewResource::collection($histories);
	}

	/**
	 * @return TaskResource|ErrorResponse
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws UnprocessableEntityHttpException
	 */
	public function actionRestore(int $id)
	{
		$identity = $this->user->identity;

		if ($identity->isAdministrator()) {
			$model = $this->repository->findModelByIdWithDeleted($id);
		} else {
			$model = $this->findModelByIdAndCreatedByWithDeleted($id);
		}

		try {
			$this->taskStateService->restore($model, $identity);

			return new TaskResource($model);
		} catch (InvalidCallException $e) {
			return $this->errorf('Задача #%s не может быть восстановлена', [$id]);
		}
	}


	/**
	 * @throws ModelNotFoundException
	 */
	public function actionFiles(int $id): array
	{
		$task = $this->repository->findModelById($id);

		return MediaResource::collection($task->files);
	}

	/**
	 * @return MediaResource[]
	 * @throws ValidateException
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 */
	public function actionCreateFiles(int $id): array
	{
		$task = $this->repository->findModelById($id);

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_TASK);

		$this->taskService->createFiles($task, $mediaForm->getDtos());

		return MediaResource::collection($task->files);
	}

	/**
	 * @return MediaResource[]|ErrorResponse
	 * @throws ModelNotFoundException
	 * @throws Throwable
	 * @throws ValidateException
	 * @throws StaleObjectException
	 */
	public function actionDeleteFiles(int $id)
	{
		$task = $this->repository->findModelById($id);

		$mediaForm = new MediaForm();

		$mediaForm->setScenario(MediaForm::SCENARIO_DELETE);

		$mediaForm->load($this->request->post());

		$mediaForm->validateOrThrow();

		try {
			$this->taskService->deleteFiles($task, $mediaForm->getDtos());

			return MediaResource::collection($task->files);
		} catch (RelationNotExistsException $e) {
			return $this->errorf("Файл %s не связан с данной задачей", $e->data);
		}
	}


	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedBy(int $id): Task
	{
		return $this->repository->findModelByIdAndCreatedBy($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedByWithDeleted(int $id): Task
	{
		return $this->repository->findModelByIdAndCreatedByWithDeleted($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @throws ModelNotFoundException
	 */
	protected function findModelByIdAndCreatedByOrUserId(int $id): Task
	{
		return $this->repository->findModelByIdAndCreatedByOrUserId($id, $this->user->id, $this->user->identity::getMorphClass());
	}

	/**
	 * @throws ValidateException
	 */
	private function makeMediaForm(string $category, string $name = 'files'): MediaForm
	{
		$form = new MediaForm();

		$form->setScenario(MediaForm::SCENARIO_CREATE);

		$form->category   = $category;
		$form->model_id   = $this->user->id;
		$form->model_type = $this->user->identity::getMorphClass();

		$form->files = UploadedFile::getInstancesByName($name);

		$form->validateOrThrow();

		return $form;
	}
}
