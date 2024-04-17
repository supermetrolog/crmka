<?php

namespace app\controllers\ChatMember;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\ChatMemberMessage;
use app\models\forms\ChatMember\ChatMemberMessageForm;
use app\models\forms\Task\TaskForm;
use app\models\search\ChatMemberMessageSearch;
use app\resources\ChatMember\ChatMemberMessageResource;
use app\resources\TaskResource;
use app\usecases\ChatMember\ChatMemberMessageService;
use app\usecases\TaskService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Connection;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

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
	 * @throws SaveModelException
	 * @throws ValidateException
	 */
	public function actionCreate(): ChatMemberMessageResource
	{
		$form = new ChatMemberMessageForm();

		$form->setScenario(ChatMemberMessageForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->from_chat_member_id = $this->user->identity->chatMember->id;

		$form->validateOrThrow();

		$model = $this->service->create($form->getDto());

		return new ChatMemberMessageResource($model);
	}

	/**
	 * @throws SaveModelException
	 * @throws NotFoundHttpException
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

		$messageTask = $this->service->createTask($message, $taskForm->getDto());

		return TaskResource::make($messageTask->task);
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