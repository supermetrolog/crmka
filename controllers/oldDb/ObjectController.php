<?php

namespace app\controllers\oldDb;

use app\behaviors\BaseControllerBehaviors;
use yii\rest\ActiveController;
use Yii;
use app\models\Company;
use app\models\oldDb\ObjectsSearch;
use app\models\oldDb\OfferMixSearch;
use app\models\UploadFile;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;

class ObjectController extends ActiveController
{
    public $modelClass = 'app\models\Company';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'view', 'offers']);
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
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new ObjectsSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    public function actionOffers()
    {
        $searchModel = new OfferMixSearch();
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
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
