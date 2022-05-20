<?php

namespace app\controllers;

use app\behaviors\BaseControllerBehaviors;
use yii\rest\ActiveController;
use Yii;
use app\models\Company;
use app\models\CompanySearch;
use app\models\Productrange;
use app\models\UploadFile;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;

class CompanyController extends ActiveController
{
    public $modelClass = 'app\models\Company';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'view']);
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
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
        return $searchModel->search(Yii::$app->request->queryParams);
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
        return Company::createCompany($request, $model);
    }
    public function actionUpdate($id)
    {
        $request = json_decode(Yii::$app->request->post('data'), true);
        $model = new UploadFile();

        $model->files = UploadedFile::getInstancesByName('files');
        return Company::updateCompany($this->findModel($id), $request, $model);
    }
    public function actionSearch()
    {
        $search = new CompanySearch();
        $searchByAttr['CompanySearch'] = Yii::$app->request->queryParams;
        return $search->search($searchByAttr);
    }
    public function actionProductRangeList()
    {
        return ArrayHelper::getColumn(Productrange::find()->select('product')->distinct()->asArray()->all(), 'product');
    }
    public function actionInTheBankList()
    {
        return ArrayHelper::getColumn(Company::find()->select('inTheBank')->where(['is not', 'inTheBank', new \yii\db\Expression('null')])->distinct()->asArray()->all(), 'inTheBank');
    }
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
