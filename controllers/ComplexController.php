<?php

namespace app\controllers;

use app\models\Complex;
use yii\web\NotFoundHttpException;

class ComplexController extends AppController
{
    /**
     * @param int $id
     * @return Complex
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Complex
    {
        return $this->findModel($id);
    }

    /**
     * @param int $id
     * @return Complex
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Complex
    {
        if ($model = Complex::find()->with(['objects'])->byId($id)->one()) {
            return $model;
        }

        throw new NotFoundHttpException('Complex not found');
    }
}