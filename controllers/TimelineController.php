<?php

namespace app\controllers;

use app\models\Timeline;
use app\models\miniModels\TimelineStep;
use app\models\miniModels\TimelineStepObject;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use Yii;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class TimelineController extends ActiveController
{
    public $modelClass = 'app\models\Timeline';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['index', 'view', 'create', 'delete', 'update', 'update-step', 'add-objects', 'options'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }
    public function actionUpdate($id)
    {
        return $id;
    }
    public function actionUpdateStep($id)
    {
        return TimelineStep::updateTimelineStep($id, Yii::$app->request->post());
    }
    public function actionAddObjects($id)
    {
        return TimelineStepObject::addObjects($id, Yii::$app->request->post());
    }
    public function actionIndex()
    {
        $consultant_id = Yii::$app->request->getQueryParam('consultant_id');
        $request_id = Yii::$app->request->getQueryParam('request_id');
        return Timeline::getTimeline($consultant_id, $request_id);
    }
    protected function findModel($id)
    {
        if (($model = Timeline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
