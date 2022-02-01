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

class CompanyController extends ActiveController
{
    public $modelClass = 'app\models\Company';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return BaseControllerBehaviors::getBaseBehaviors($behaviors, ['*']);
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
        file_put_contents(Yii::getAlias('@app') . '/exceptions/test.txt', '1234');
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
