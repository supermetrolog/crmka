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
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => [
                'index', 'company-requests', 'view', 'search'
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
