<?php

namespace app\controllers;

use app\models\search\ObjectsSearch;
use yii\data\ActiveDataProvider;

class ObjectsController extends AppController
{
    /**
     * @return ActiveDataProvider
     */
    public function actionIndex(): ActiveDataProvider
    {
        $searchModel = new ObjectsSearch();

        return $searchModel->search($this->request->getQueryParams());
    }
}