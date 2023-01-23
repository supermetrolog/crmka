<?php

namespace app\controllers;

use Yii;
use app\models\Request;
use app\models\RequestSearch;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\behaviors\BaseControllerBehaviors;
use app\models\request\RequestDisable;
use app\models\request\RequestUnDisable;
use app\models\Timeline;
use Symfony\Component\HttpFoundation\JsonResponse;
use yii\filters\Cors;

/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends ActiveController
{
    public $modelClass = 'app\models\Request';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
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
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['delete']);
        unset($actions['update']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
    public function actionCompanyRequests($id)
    {
        return Request::getCompanyRequestsList($id);
    }
    public function actionView($id)
    {
        return Request::getRequestInfo($id);
    }
    public function actionCreate()
    {
        return Request::createRequest(Yii::$app->request->post());
    }
    public function actionUpdate($id)
    {
        return Request::updateRequest($this->findModel($id), Yii::$app->request->post());
    }
    public function actionSearch()
    {
        $search = new RequestSearch();
        $searchByAttr['CompanySearch'] = Yii::$app->request->queryParams;
        return $search->search($searchByAttr);
    }
    public function actionDisable($id)
    {
        $disableModel = new RequestDisable($this->findModel($id), Timeline::getActiveTimelineForRequest($id), Yii::$app->request->post());
        $disableModel->disableRequestAndTimeline();
        return [
            'data' => true,
            'message' => "Запрос переведен в пассив"
        ];
    }
    public function actionUndisable($id)
    {
        $request = $this->findModel($id);

        $disableModel = new RequestUnDisable($request, Timeline::getTimelineByRequestAndConsultantID($request->id, $request->consultant_id));
        $disableModel->unDisableRequestAndTimeline();
        return [
            'data' => true,
            'message' => "Запрос переведен в актив"
        ];
    }
    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
