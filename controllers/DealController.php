<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\exceptions\ValidationErrorHttpException;
use app\models\Deal;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use Yii;

class DealController extends ActiveController
{
    public $modelClass = 'app\models\Deal';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ["*"]);
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete'], $actions['create'], $actions['update']);
        return $actions;
    }
    public function actionCreate()
    {
        return Deal::createDeal(Yii::$app->request->post());
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return Deal::updateDeal($model, Yii::$app->request->post());
    }
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = DEAL::STATUS_DELETED;
        if ($model->save()) {
            return ['message' => "Сделка удалена", 'data' => $id];
        }
        throw new ValidationErrorHttpException($model->getErrorSummary(false));
    }
    protected function findModel($id)
    {
        if (($model = Deal::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
