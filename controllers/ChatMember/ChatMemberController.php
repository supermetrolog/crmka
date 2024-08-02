<?php

namespace app\controllers\ChatMember;

use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\common\models\exceptions\ValidateException;
use app\kernel\web\http\resources\JsonResource;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\ChatMember;
use app\models\forms\Call\CallForm;
use app\models\forms\ChatMember\PinChatMemberMessageForm;
use app\models\forms\ChatMember\UnpinChatMemberMessageForm;
use app\models\search\ChatMemberMediaSearch;
use app\models\search\ChatMemberSearch;
use app\models\User;
use app\repositories\ChatMemberRepository;
use app\resources\CallResource;
use app\resources\ChatMember\ChatMemberFullResource;
use app\resources\ChatMember\ChatMemberMessageResource;
use app\resources\ChatMember\ChatMemberResource;
use app\resources\MediaResource;
use app\usecases\ChatMember\ChatMemberService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ChatMemberController extends AppController
{
	private ChatMemberService    $service;
	private ChatMemberRepository $repository;

	public function __construct($id, $module, ChatMemberService $service, ChatMemberRepository $repository, array $config = [])
	{
		$this->service    = $service;
		$this->repository = $repository;
		parent::__construct($id, $module, $config);
	}

	/**
	 * @throws ValidateException
	 * @throws ErrorException
	 */
	public function actionIndex(): ActiveDataProvider
	{
		// TODO: Сделать разные поиски для разны типов моделей так как они будут сильно отличаться!

		$searchModel = new ChatMemberSearch();

		$searchModel->current_chat_member_id = $this->user->identity->chatMember->id;
		$searchModel->current_user_id        = $this->user->id;

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

	public function actionStatistic(): array
	{
		return $this->repository->getStatisticByIds(
			$this->request->get('chat_member_ids')
		);
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
	 * @param int $id
	 *
	 * @return ActiveDataProvider
	 * @throws ErrorException
	 * @throws ModelNotFoundException
	 * @throws ValidateException
	 */
	public function actionMedia(int $id): ActiveDataProvider
	{
		$fromMemberChat = ChatMember::find()
		                            ->byMorph($this->user->id, User::getMorphClass())
		                            ->oneOrThrow();

		$searchModel = new ChatMemberMediaSearch();

		$searchModel->to_member_chat_id   = $id;
		$searchModel->from_member_chat_id = $fromMemberChat->id;

		$dataProvider = $searchModel->search($this->request->get());

		return MediaResource::fromDataProvider($dataProvider);
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
	 * @return SuccessResponse
	 * @throws ValidateException
	 * @throws SaveModelException
	 */
	public function actionUnpinMessage(): SuccessResponse
	{
		$form = new UnpinChatMemberMessageForm();

		$form->load($this->request->post());

		$form->validateOrThrow();

		$this->service->unpinMessage($form->getChatMember());

		return new SuccessResponse();
	}

	/**
	 * @param int $id
	 *
	 * @return CallResource
	 * @throws ValidateException
	 * @throws Throwable
	 */
	public function actionCalled(int $id): CallResource
	{
		$form = new CallForm();

		$form->setScenario(CallForm::SCENARIO_CREATE);

		$form->load($this->request->post());

		$form->validateOrThrow();

		$model = $this->service->createCall($this->findModel($id), $form->getDto());

		return new CallResource($model);
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
