<?php

declare(strict_types=1);

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use app\models\company\eventslog\CompanyEventsLog;
use app\models\company\eventslog\CompanyEventsLogSearch;
use app\models\company\eventslog\CreateCompanyEvent;
use Yii;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

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
        unset($actions['index']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchEventModel = new CompanyEventsLogSearch();
        return $searchEventModel->search(Yii::$app->request->getQueryParams());
    }
    public function actionCreate()
    {
        $createEventModel = new CreateCompanyEvent(Yii::$app->request->post());
        $createEventModel->create();
        return ['message' => 'Событие создано', 'data' => $createEventModel->id];
        $model = $this->findModel($createEventModel->id);
        return ['message' => 'Событие создано', 'data' => $model->toArray([], ['user.userProfile'], true)];
    }

    private function findModel(int $id): CompanyEventsLog
    {
        if ($model = CompanyEventsLog::find()->with(['user.userProfile'])->where(['id' => $id])->limit(1)->one()) {
            return $model;
        }

        throw new NotFoundHttpException("company event log not found");
    }
}
