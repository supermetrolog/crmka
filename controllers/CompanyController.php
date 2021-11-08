<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\filters\Cors;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use app\models\Company;
use app\models\CompanySearch;
use app\models\UploadFile;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;

class CompanyController extends ActiveController
{
    public $modelClass = 'app\models\Company';

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
            'except' => ['search', 'view', 'index', 'options', 'create', 'update'],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }
    public function actionIndex()
    {
        return Company::getCompanyList();
    }
    public function actionView($id)
    {
        return Company::getCompanyInfo($id);
    }
    public function actionCreate()
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();

        $model->files = UploadedFile::getInstancesByName('files');
        // return $model->validate();
        return Yii::$app->request->post();

        return Company::createCompany($request, $model);
    }
    public function actionUpdate($id)
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();

        $model->files = UploadedFile::getInstancesByName('files');
        return Yii::$app->request->post();
        return Company::updateCompany($this->findModel($id), $request);
    }
    public function actionSearch()
    {
        $search = new CompanySearch();
        $searchByAttr['CompanySearch'] = Yii::$app->request->queryParams;
        return $search->search($searchByAttr);
    }
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
