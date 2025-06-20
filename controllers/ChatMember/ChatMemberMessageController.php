<?php

namespace app\controllers\ChatMember;

use app\dto\Task\LinkTaskRelationEntityDto;
use app\helpers\ArrayHelper;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\ChatMemberMessage;
use app\models\forms\Alert\AlertForm;
use app\models\forms\ChatMember\ChatMemberMessageForm;
use app\models\forms\ChatMember\ViewChatMemberMessageForm;
use app\models\forms\Media\MediaForm;
use app\models\forms\Notification\NotificationForm;
use app\models\forms\Reminder\ReminderForm;
use app\models\forms\Task\TaskForm;
use app\models\forms\Task\TaskRelationEntityLinkForm;
use app\models\Media;
use app\models\search\ChatMemberMessageSearch;
use app\resources\AlertResource;
use app\resources\ChatMemberMessage\ChatMemberMessageResource;
use app\resources\ReminderResource;
use app\resources\Task\TaskResource;
use app\resources\UserNotificationResource;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ChatMemberMessageController extends AppController
{
	private ChatMemberMessageService $service;

	public function __construct(
		$id,
		$module,
		ChatMemberMessageService $service,
		array $config = []
	)
	{
		$this->service = $service;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new ChatMemberMessageSearch();

		$searchModel->current_chat_member_id = $this->user->identity->chatMember->id;

		$dataProvider = $searchModel->search($this->request->get());

		return ChatMemberMessageResource::fromDataProvider($dataProvider);
	}

	/**
	 * @return ChatMemberMessageResource
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreate(): ChatMemberMessageResource
	{
		$form = new ChatMemberMessageForm();

		$form->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->from_chat_member_id = $this->user->identity->chatMember->id;

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_CHAT_MEMBER_MESSAGE);

		$form->files = $mediaForm->files;

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto(), $mediaForm->getDtos());

		return new ChatMemberMessageResource($model);
	}

	/**
	 * @param int $id
	 *
	 * @return ChatMemberMessageResource
	 * @throws NotFoundHttpException
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionUpdate(int $id): ChatMemberMessageResource
	{
		$model = $this->findModel($id);

		$form = new ChatMemberMessageForm();

		$form->setScenario(ChatMemberMessageForm::SCENARIO_UPDATE);

		$form->load($this->request->post());

		$mediaForm = $this->makeMediaForm(Media::CATEGORY_CHAT_MEMBER_MESSAGE);

		$form->files = $mediaForm->files;
		$form->validateOrThrow();

		$this->service->update($model, $form->getDto(), $mediaForm->getDtos());

		return new ChatMemberMessageResource($model);
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 * @throws NotFoundHttpException
	 */
	public function actionDelete(int $id): void
	{
		$this->findModel($id)->delete();
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateWithTask(): ChatMemberMessageResource
	{
		$chatMemberMessageForm = new ChatMemberMessageForm();

		$chatMemberMessageForm->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$chatMemberMessageForm->load($this->request->post());

		$chatMemberMessageForm->from_chat_member_id = $this->user->identity->chatMember->id;

		$chatMemberMessageForm->validateOrThrow();

		$taskForm = $this->makeTaskForm($this->request->post('task'));

		$model = $this->service->createWithTask($chatMemberMessageForm->getDto(), $taskForm->getDto());

		return ChatMemberMessageResource::make($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 * @throws Throwable
	 * @throws ValidateException
	 */
	public function actionCreateWithTasks(): ChatMemberMessageResource
	{
		$chatMemberMessageForm = new ChatMemberMessageForm();

		$chatMemberMessageForm->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$chatMemberMessageForm->load($this->request->post());

		$chatMemberMessageForm->from_chat_member_id = $this->user->identity->chatMember->id;

		$chatMemberMessageForm->validateOrThrow();

		$taskDtos = [];

		foreach ($this->request->post('tasks', []) as $taskData) {
			$taskForm   = $this->makeTaskForm($taskData);
			$taskDtos[] = $taskForm->getDto();
		}

		$model = $this->service->createWithTasks($chatMemberMessageForm->getDto(), $taskDtos);

		return ChatMemberMessageResource::make($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateTask(int $id): TaskResource
	{
		$message = $this->findModel($id, false);

		$taskForm           = $this->makeTaskForm($this->request->post());
		$mediaForm          = $this->makeMediaForm(Media::CATEGORY_TASK);
		$relationEntityDtos = $this->makeRelationEntityDtos($this->request->post('relations', []));

		$task = $this->service->createTask($message, $taskForm->getDto(), $mediaForm->getDtos(), $relationEntityDtos);

		return TaskResource::make($task);
	}

	/**
	 * @return TaskResource[]
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateTasks(int $id): array
	{
		$message = $this->findModel($id, false);

		$taskDtos           = [];
		$relationEntityDtos = [];

		foreach ($this->request->post('tasks', []) as $taskData) {
			$taskForm   = $this->makeTaskForm($taskData);
			$taskDtos[] = $taskForm->getDto();

			$relationEntityDtos[] = $this->makeRelationEntityDtos(ArrayHelper::getValue($taskData, 'relations', []));
		}

		$tasks = $this->service->createTasks($message, $taskDtos, $relationEntityDtos);

		return TaskResource::collection($tasks);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateAlert(int $id): AlertResource
	{
		$message = $this->findModel($id, false);

		$alertForm = new AlertForm();

		$alertForm->setScenario(AlertForm::SCENARIO_CREATE);

		$alertForm->load($this->request->post());

		$alertForm->created_by_id   = $this->user->id;
		$alertForm->created_by_type = $this->user->identity::getMorphClass();

		$alertForm->validateOrThrow();

		$alert = $this->service->createAlert($message, $alertForm->getDto());

		return AlertResource::make($alert);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateReminder(int $id): ReminderResource
	{
		$message = $this->findModel($id, false);

		$reminderForm = new ReminderForm();

		$reminderForm->setScenario(ReminderForm::SCENARIO_CREATE);

		$reminderForm->load($this->request->post());

		$reminderForm->created_by_id   = $this->user->id;
		$reminderForm->created_by_type = $this->user->identity::getMorphClass();

		$reminderForm->validateOrThrow();

		$reminder = $this->service->createReminder($message, $reminderForm->getDto());

		return ReminderResource::make($reminder);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCreateNotification(int $id): UserNotificationResource
	{
		$message = $this->findModel($id, false);

		$notificationForm = new NotificationForm();

		$notificationForm->setScenario(NotificationForm::SCENARIO_CREATE);

		$notificationForm->load($this->request->post());

		$notificationForm->validateOrThrow();

		$userNotification = $this->service->createNotification($message, $notificationForm->getDto());

		return UserNotificationResource::make($userNotification);
	}

	/**
	 * @throws SaveModelException
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionViewMessage(int $id): SuccessResponse
	{
		$form = new ViewChatMemberMessageForm();

		$form->load($this->request->post());

		$form->chat_member_message_id = $id;
		$form->from_chat_member_id    = $this->user->identity->chatMember->id;

		$form->validateOrThrow();

		$this->service->viewMessages($form->getChatMemberMessage(), $form->from_chat_member_id);

		return new SuccessResponse();
	}


	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id, bool $checkOwner = true): ChatMemberMessage
	{
		$query = ChatMemberMessage::find()
		                          ->with(['fromChatMember'])
		                          ->byId($id)
		                          ->notDeleted();

		if ($checkOwner) {
			$query->byFromChatMemberId($this->user->identity->chatMember->id);
		}

		if ($model = $query->one()) {
			return $model;
		}

		throw new NotFoundHttpException('The requested model does not exist.');
	}

	/**
	 * @throws ValidateException
	 */
	private function makeMediaForm(string $category, string $name = 'files'): MediaForm
	{
		$form = new MediaForm();

		$form->category   = $category;
		$form->model_id   = $this->user->id;
		$form->model_type = $this->user->identity::getMorphClass();

		$form->files = UploadedFile::getInstancesByName($name);

		$form->validateOrThrow();

		return $form;
	}


	/**
	 * @throws ValidateException
	 */
	private function makeTaskForm(array $taskData): TaskForm
	{
		$taskForm = new TaskForm();

		$taskForm->setScenario(TaskForm::SCENARIO_CREATE);

		$taskForm->load($taskData);

		$taskForm->created_by_id   = $this->user->id;
		$taskForm->created_by_type = $this->user->identity::getMorphClass();

		$taskForm->validateOrThrow();

		return $taskForm;
	}

	/**
	 * @throws ValidateException
	 */
	private function makeRelationEntityLinkForm(array $payload): TaskRelationEntityLinkForm
	{
		$form = new TaskRelationEntityLinkForm();

		$form->load($payload);

		$form->validateOrThrow();

		return $form;
	}

	/**
	 * @return LinkTaskRelationEntityDto[]
	 * @throws ValidateException
	 * @throws \Exception
	 */
	private function makeRelationEntityDtos(array $payload): array
	{
		$dtos = [];

		foreach ($payload as $element) {
			$form = $this->makeRelationEntityLinkForm($element);

			$dto = $form->getDto();

			$dto->createdBy = $this->user->identity;

			$dtos[] = $dto;
		}

		return $dtos;
	}
}
