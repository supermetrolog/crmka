<?php

namespace app\controllers;

use Yii;
use app\models\Notification;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use app\behaviors\BaseControllerBehaviors;
use app\models\NotificationSearch;

/**
 * NotificationController implements the CRUD actions for Notification model.
 */
class NotificationController extends ActiveController
{
    public $modelClass = 'app\models\Notification';
    // public $serializer = [
    //     'class' => 'yii\rest\Serializer',
    //     'collectionEnvelope' => 'items',
    // ];
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors =  BaseControllerBehaviors::getBaseBehaviors($behaviors, []);
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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }
    /**
     * Lists all Notification models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NotificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $models = $dataProvider->getModels();
        $copyModels = Notification::array_copy($models);
        Notification::changeNoViewedStatusToNoCount($models);
        $dataProvider->models = $copyModels;
        return $dataProvider;
    }
    public function actionViewedNotCount($id)
    {
        return Notification::viewedNotCount($id);
    }
    public function actionViewedAll($id)
    {
        return Notification::viewedAll($id);
    }
    public function actionCount($id)
    {
        return Notification::getNotificationsCount($id);
    }
}
