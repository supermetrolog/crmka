<?php

declare(strict_types=1);

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\models\company\eventslog\CreateCompanyEvent;
use Yii;
use yii\filters\Cors;
use yii\rest\ActiveController;

class CompanyEventsLogController extends ActiveController
{
    public $modelClass = 'app\models\company\eventslog\CompanyEventsLog';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'create']);
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
        unset($actions['create']);
        return $actions;
    }

    public function actionCreate()
    {
        $createEventModel = new CreateCompanyEvent(Yii::$app->request->post());
        $createEventModel->create();
        return ['message' => 'Событие создано', 'data' => $createEventModel->id];
    }
}
