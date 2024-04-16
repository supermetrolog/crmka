<?php

namespace app\controllers\ChatMember;

use app\exceptions\domain\model\SaveModelException;
use app\exceptions\domain\model\ValidateException;
use app\kernel\common\controller\AppController;
use app\models\ChatMemberMessage;
use app\models\forms\ChatMember\ChatMemberMessageForm;
use app\models\search\ChatMemberMessageSearch;
use app\resources\ChatMember\ChatMemberMessageResource;
use app\usecases\ChatMember\ChatMemberMessageService;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

class ChatMemberMessageController extends AppController
{
	private ChatMemberMessageService $service;

	public function __construct($id, $module, ChatMemberMessageService $service, array $config = [])
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
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?ChatMemberMessage
	{
		$model = ChatMemberMessage::find()
		                          ->with(['fromChatMember'])
		                          ->byId($id)
		                          ->byFromChatMemberId($this->user->identity->chatMember->id)
		                          ->notDeleted()
		                          ->one();

		if ($model) {
			return $model;
		}

		throw new NotFoundHttpException('The requested model does not exist.');
	}
}
