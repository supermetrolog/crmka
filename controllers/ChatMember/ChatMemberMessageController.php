<?php

namespace app\controllers\ChatMember;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\models\ChatMemberMessage;
use app\models\forms\Alert\AlertForm;
use app\models\forms\ChatMember\ChatMemberMessageForm;
use app\models\forms\Media\MediaForm;
use app\models\forms\Notification\NotificationForm;
use app\models\forms\Reminder\ReminderForm;
use app\models\forms\Task\TaskForm;
use app\models\search\ChatMemberMessageSearch;
use app\resources\AlertResource;
use app\resources\ChatMember\ChatMemberMessageResource;
use app\resources\ReminderResource;
use app\resources\TaskResource;
use app\resources\UserNotificationResource;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
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
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new ChatMemberMessageSearch();

		$dataProvider = $searchModel->search(Yii::$app->request->get());

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

		$form->validateOrThrow();

		$mediaForm = new MediaForm();

		$mediaForm->category   = ChatMemberMessage::DEFAULT_MEDIA_CATEGORY;
		$mediaForm->model_id   = $this->user->id;
		$mediaForm->model_type = $this->user->identity::getMorphClass();

		$mediaForm->files = UploadedFile::getInstancesByName('files');

		$mediaForm->validateOrThrow();

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
		$form->validateOrThrow();

		$this->service->update($model, $form->getDto());

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
		$taskForm = new TaskForm();

		$taskForm->setScenario(TaskForm::SCENARIO_CREATE);

		$taskForm->load($this->request->post());

		$taskForm->created_by_id   = $this->user->id;
		$taskForm->created_by_type = $this->user->identity::getMorphClass();

		$taskForm->validateOrThrow();

		$chatMemberMessageForm = new ChatMemberMessageForm();

		$chatMemberMessageForm->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$chatMemberMessageForm->load($this->request->post());

		$chatMemberMessageForm->from_chat_member_id = $this->user->identity->chatMember->id;

		$chatMemberMessageForm->validateOrThrow();

		$model = $this->service->createWithTask($chatMemberMessageForm->getDto(), $taskForm->getDto());

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

		$taskForm = new TaskForm();

		$taskForm->setScenario(TaskForm::SCENARIO_CREATE);

		$taskForm->load($this->request->post());

		$taskForm->created_by_id   = $this->user->id;
		$taskForm->created_by_type = $this->user->identity::getMorphClass();

		$taskForm->validateOrThrow();

		$task = $this->service->createTask($message, $taskForm->getDto());

		return TaskResource::make($task);
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
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id, bool $checkOwner = true): ?ChatMemberMessage
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
}
