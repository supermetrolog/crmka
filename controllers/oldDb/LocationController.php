<?php

namespace app\controllers\oldDb;

use app\behaviors\BaseControllerBehaviors;
use yii\rest\ActiveController;
use Yii;
use app\models\Company;
use app\models\oldDb\location\Region;
use app\models\oldDb\ObjectsSearch;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;

class LocationController extends ActiveController
{
    public $modelClass = 'app\models\oldDb\location\Location';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BaseControllerBehaviors::getBaseBehaviors($behaviors, ['index', 'region-list']);
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
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
    public function actionIndex()
    {
        $searchModel = new ObjectsSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
    public function actionRegionList()
    {
        $models = Region::find()->orderBy(['id' => new Expression("FIELD(id, 1, 6) DESC")])->all();
        $list = array_map(function ($elem) {
            return ['value' => $elem->id, 'label' => mb_strtolower($elem->title)];
        }, $models);
        return array_merge([
            [
                'value' => 'mskandmo',
                'label' => 'Москва и МО'
            ],
            [
                'value' => "mskinsidemkad",
                'label' => 'Москва внутри МКАД'
            ],
            [
                'value' => 'moandmskoutsidemkad',
                'label' => 'МО + Москва снаружи МКАД'
            ],
            [
                'value' => 'moandregionneardy',
                'label' => 'МО + регионы рядом'
            ]
        ], $list);
    }
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
