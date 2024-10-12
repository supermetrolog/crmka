<?php

namespace app\controllers\oldDb;

use app\behaviors\BaseControllerBehaviors;
use app\exceptions\ValidationErrorHttpException;
use app\models\Company;
use app\models\OfferMix;
use app\models\oldDb\ObjectsSearch;
use app\models\oldDb\OfferMixMapSearch;
use app\models\oldDb\OfferMixSearch;
use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ObjectController extends ActiveController
{
	public $modelClass = Company::class;

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		$behaviors               = parent::behaviors();
		$behaviors               = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'view']);
		$behaviors['corsFilter'] = [
			'class' => Cors::class,
			'cors'  => [
				'Origin'                         => ['*'],
				'Access-Control-Request-Method'  => ['*'],
				'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
				'Access-Control-Expose-Headers'  => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page', 'Link']
			],
		];

		return $behaviors;
	}

	/**
	 * @return array
	 */
	public function actions(): array
	{
		$actions = parent::actions();
		unset($actions['index']);
		unset($actions['view']);
		unset($actions['create']);
		unset($actions['update']);
		unset($actions['delete']);

		return $actions;
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
		$this->response->format = Response::FORMAT_JSON;
		$this->response->on(Response::EVENT_BEFORE_SEND, function () {
			$this->response->headers->remove('link');
		});

		// TODO: Change to $this->user when controller is switched to rest controller
		$identity = Yii::$app->user->identity;

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
		$this->response->format = Response::FORMAT_JSON;
		$this->response->on(Response::EVENT_BEFORE_SEND, function () {
			$this->response->headers->remove('link');
		});

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
		$this->response->format = Response::FORMAT_JSON;
		$this->response->on(Response::EVENT_BEFORE_SEND, function () {
			$this->response->headers->remove('link');
		});

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
		$this->response->on(Response::EVENT_BEFORE_SEND, function () {
			$this->response->headers->remove('link');
		});

		$searchModel = new OfferMixMapSearch();

		return $searchModel->search($this->request->get());
	}

	/**
	 * @param int $originalId
	 *
	 * @return void
	 * @throws NotFoundHttpException|ErrorException
	 */
	// TODO: Move to service
	public function actionToggleAvitoAd(int $originalId): void
	{
		$model = OfferMix::find()->byOriginalId($originalId)->one();

		if (!$model) {
			throw new NotFoundHttpException('Offer not found');
		}

		$block = $model->block;

		if (!$block) {
			throw new NotFoundHttpException('Offer block not found');
		}

		$block->ad_avito = !$block->ad_avito;
		$model->ad_avito = $block->ad_avito;

		if ($block->ad_avito) {
			$block->ad_avito_date_start = date('Y-m-d H:i:s');
		} else {
			$block->ad_avito_date_start = null;
		}

		$model->save(false);
		$block->save(false);
	}

	/**
	 * @param int $originalId
	 *
	 * @return void
	 * @throws NotFoundHttpException|ErrorException
	 */

	// TODO: Move to service
	public function actionToggleIsFake(int $originalId): void
	{
		$model = OfferMix::find()->byOriginalId($originalId)->one();

		if (!$model) {
			throw new NotFoundHttpException('Offer not found');
		}

		$block = $model->block;

		if (!$block) {
			throw new NotFoundHttpException('Offer block not found');
		}

		$block->is_fake = !$block->is_fake;
		$model->is_fake = $block->is_fake;

		$model->save(false);
		$block->save(false);
	}
}
