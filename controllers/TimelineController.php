<?php

namespace app\controllers;

use app\models\Timeline;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class TimelineController extends ActiveController
{
    public $modelClass = 'app\models\Timeline';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => [
                'index', 'view'
            ]
        ];
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['*'],
                'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization'],
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }
    public function actionIndex($id)
    {
        return $id;
    }
    public function actionTimeline()
    {
        return 3;
    }
    protected function findModel($id)
    {
        if (($model = Timeline::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
