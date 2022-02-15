<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use yii\rest\ActiveController;
use app\models\Calendar;
use Yii;
use yii\web\NotFoundHttpException;

class CalendarController extends ActiveController
{
    public $modelClass = 'app\models\Calendar';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }
    public function actionCreate()
    {
        return Calendar::createCalendarItem(Yii::$app->request->post());
    }
    protected function findModel($id)
    {
        if (($model = Calendar::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
