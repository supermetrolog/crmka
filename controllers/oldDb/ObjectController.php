<?php

namespace app\controllers\oldDb;

use app\behaviors\BaseControllerBehaviors;
use app\exceptions\ValidationErrorHttpException;
use app\models\OfferMix;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use Yii;
use app\models\Company;
use app\models\oldDb\ObjectsSearch;
use app\models\oldDb\OfferMixMapSearch;
use app\models\oldDb\OfferMixSearch;
use app\models\UploadFile;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use yii\web\Response;

class ObjectController extends ActiveController
{
    public $modelClass = 'app\models\Company';

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'view']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
                'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page', 'Link']
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
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * @return ActiveDataProvider
     * @throws ValidationErrorHttpException
     */
    public function actionOffers(): ActiveDataProvider
    {
        $this->response->format = Response::FORMAT_JSON;
        $searchModel = new OfferMixSearch();
        $this->response->on(Response::EVENT_BEFORE_SEND, function () {
            $this->response->headers->remove('link');
        });
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * @return int|string|null
     * @throws ValidationErrorHttpException
     */
    public function actionOffersCount()
    {
        $this->response->format = Response::FORMAT_JSON;
        $searchModel = new OfferMixSearch();
        $this->response->on(Response::EVENT_BEFORE_SEND, function () {
            $this->response->headers->remove('link');
        });
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $dataProvider->query->count();
    }

    /**
     * @return int|string|null
     * @throws ValidationErrorHttpException
     */
    public function actionOffersMapCount()
    {
        $this->response->format = Response::FORMAT_JSON;
        $searchModel = new OfferMixMapSearch();
        $this->response->on(Response::EVENT_BEFORE_SEND, function () {
            $this->response->headers->remove('link');
        });
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $dataProvider->query->count();
    }

    /**
     * @return ActiveDataProvider
     * @throws ValidationErrorHttpException
     */
    public function actionOffersMap(): ActiveDataProvider
    {
        $searchModel = new OfferMixMapSearch();
        $this->response->on(Response::EVENT_BEFORE_SEND, function () {
            $this->response->headers->remove('link');
        });
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * @param int $originalId
     * @return void
     * @throws NotFoundHttpException
     */
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
     * @return void
     * @throws NotFoundHttpException
     */
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
