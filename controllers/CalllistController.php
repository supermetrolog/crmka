<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\models\CallList;
use yii\rest\ActiveController;
use yii\filters\Cors;
use Yii;
use app\models\CallListSearch;
// use yii\filters\auth\HttpBearerAuth;

/**
 * CallListController implements the CRUD actions for CallList model.
 */
class CalllistController extends ActiveController
{
    public $modelClass = 'app\models\CallList';
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
     * Lists all CallList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $models = $dataProvider->getModels();
        $copyModels = CallList::array_copy($models);
        CallList::changeNoViewedStatusToNoCount($models);
        $dataProvider->models = $copyModels;
        return $dataProvider;
    }
    public function actionViewedNotCount($caller_id)
    {
        return CallList::viewedNotCount($caller_id);
    }
    public function actionViewedAll($caller_id)
    {
        return CallList::viewedAll($caller_id);
    }
    public function actionCount($caller_id)
    {
        return CallList::getCallsCount($caller_id);
    }
}
