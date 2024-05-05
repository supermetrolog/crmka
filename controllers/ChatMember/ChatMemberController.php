<?php

namespace app\controllers\ChatMember;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\resources\JsonResource;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\ChatMember;
use app\models\forms\ChatMember\PinChatMemberMessageForm;
use app\models\search\ChatMemberSearch;
use app\resources\ChatMember\ChatMemberFullResource;
use app\resources\ChatMember\ChatMemberMessageResource;
use app\resources\ChatMember\ChatMemberResource;
use app\usecases\ChatMember\ChatMemberMessageService;
use app\usecases\ChatMember\ChatMemberService;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ChatMemberController extends AppController
{
	private ChatMemberService $service;

	public function __construct($id, $module, ChatMemberService $service, array $config = [])
	{
		$this->service = $service;
		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		// TODO: Сделать разные поиски для разны типов моделей так как они будут сильно отличаться!

		$searchModel = new ChatMemberSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return ChatMemberResource::fromDataProvider($dataProvider);
	}

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionView(int $id): JsonResource
	{
		return ChatMemberFullResource::make($this->findModel($id));
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function actionPinnedMessage(int $id): ?JsonResource
	{
		$model = ChatMember::find()
		                   ->byId($id)
		                   ->oneOrThrow();

		return ChatMemberMessageResource::tryMake($model->pinnedChatMemberMessage);
	}


	/**
	 * @return SuccessResponse
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionPinMessage(): SuccessResponse
	{
		$form = new PinChatMemberMessageForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->pinMessage($form->getChatMember(), $form->getChatMemberMessage());

		return new SuccessResponse();
	}

	/**
	 * @throws NotFoundHttpException
	 */
	protected function findModel(int $id): ?ChatMember
	{
		// TODO: add in generator

		$model = ChatMember::find()
		                   ->byId($id)
//		                   ->with(['messages' => function (ChatMemberMessageQuery $query) {
//			                   $query->notDeleted();
//		                   }])
                           ->with(['objectChatMember.object.company'])
		                   ->with([
			                   'request.company',
			                   'request.regions',
			                   'request.directions',
			                   'request.districts',
			                   'request.objectTypes',
			                   'request.objectClasses',
		                   ])
		                   ->with(['user.userProfile'])
		                   ->one();

		if ($model) {
			return $model;
		}

		throw new NotFoundHttpException('The requested model does not exist.');
	}
}
