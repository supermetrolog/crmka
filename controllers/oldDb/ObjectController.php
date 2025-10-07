<?php

namespace app\controllers\oldDb;

use app\exceptions\ValidationErrorHttpException;
use app\kernel\common\controller\AppController;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\OfferMix;
use app\models\oldDb\ObjectsSearch;
use app\models\oldDb\OfferMixMapSearch;
use app\models\oldDb\OfferMixSearch;
use app\usecases\OfferMix\OfferMixService;
use Throwable;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class ObjectController extends AppController
{
	protected array $viewOnlyAllowedActions = ['*'];

	private OfferMixService $offerMixService;

	public function __construct(
		$id,
		$module,
		OfferMixService $offerMixService,
		array $config = []
	)
	{
		$this->offerMixService = $offerMixService;

		parent::__construct($id, $module, $config);
	}

	/**
	 * @return ActiveDataProvider
	 */
	public function actionIndex(): ActiveDataProvider
	{
		$searchModel = new ObjectsSearch();

		return $searchModel->search($this->request->get());
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidationErrorHttpException|ErrorException
	 */
	public function actionOffers(): ActiveDataProvider
	{
		$identity = $this->user->identity;

		$searchModel = new OfferMixSearch();

		$searchModel->current_chat_member_id = $identity->chatMember->id;

		return $searchModel->search($this->request->get());
	}

	/**
	 * @return int|string|null
	 * @throws ValidationErrorHttpException|ErrorException
	 */
	public function actionOffersCount()
	{
		$searchModel = new OfferMixSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return $dataProvider->query->count();
	}

	/**
	 * @return int|string|null
	 * @throws ValidationErrorHttpException
	 */
	public function actionOffersMapCount()
	{
		$searchModel = new OfferMixMapSearch();

		$dataProvider = $searchModel->search($this->request->get());

		return $dataProvider->query->count();
	}

	/**
	 * @return ActiveDataProvider
	 * @throws ValidationErrorHttpException
	 */
	public function actionOffersMap(): ActiveDataProvider
	{
		$searchModel = new OfferMixMapSearch();

		return $searchModel->search($this->request->get());
	}

	/**
	 * @param int $originalId
	 *
	 * @return SuccessResponse
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionToggleAvitoAd(int $originalId): SuccessResponse
	{
		$model = OfferMix::find()->byOriginalId($originalId)->oneOrThrow();

		$avitoAdIsActive = $this->offerMixService->toggleAvitoAd($model);

		return new SuccessResponse('Реклама на Авито успешно ' . $avitoAdIsActive ? 'подключена' : 'отключена');
	}

	/**
	 * @param int $originalId
	 *
	 * @return SuccessResponse
	 * @throws ModelNotFoundException
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function actionToggleIsFake(int $originalId): SuccessResponse
	{
		$model = OfferMix::find()->byOriginalId($originalId)->oneOrThrow();

		$offerIsFake = $this->offerMixService->toggleIsFake($model);

		return new SuccessResponse($offerIsFake ? 'Предложение помечено как фейковое' : 'Предложение больше не является фейкомым');
	}
}
