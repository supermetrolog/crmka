<?php

namespace app\controllers;

use yii\rest\ActiveController;
use app\behaviors\BaseControllerBehaviors;
use app\models\FavoriteOfferSearch;
use Yii;

class FavoriteOfferController extends ActiveController
{
    public $modelClass = 'app\models\FavoriteOffer';
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors);
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $searchModel = new FavoriteOfferSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
}
