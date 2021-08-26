<?php

namespace app\controllers;

use Yii;
use app\models\Request;
// use app\models\RequestSearch;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
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
            'except' => ['search', 'company-requests', 'index', 'view', 'options', 'create', 'delete', 'update'],
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
        return true;
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
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return ['message' => 'Запрос удален', 'data' => true];
    }
    // public function actionSearch()
    // {
    //     $search = new RequestSearch();
    //     $searchByAttr['CompanySearch'] = Yii::$app->request->queryParams;
    //     return $search->search($searchByAttr);
    // }
    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
