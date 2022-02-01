<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\models\Deal;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

class DealController extends ActiveController
{
    public $modelClass = 'app\models\Deal';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ["*"]);
    }
    protected function findModel($id)
    {
        if (($model = Deal::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
