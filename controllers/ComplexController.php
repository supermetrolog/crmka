<?php

namespace app\controllers;

use app\models\Complex;
use yii\db\ActiveQuery;
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
        $query = Complex::find()
            ->with([
                'objects.company.consultant.userProfile',
                'objects.objectClassRecord',
                'objects.firefightingType',
                'objects.location.regionRecord',
                'objects.location.highwayRecord',
                'objects.location.directionRecord',
                'objects.location.districtRecord',
                'objects.location.districtMoscowRecord',
                'objects.location.townRecord.townTypeRecord',
                'objects.location.townCentralRecord',
                'objects.location.metroRecord',
                'location.regionRecord',
                'location.highwayRecord',
                'location.directionRecord',
                'location.districtRecord',
                'location.districtMoscowRecord',
                'location.townRecord.townTypeRecord',
                'location.townCentralRecord',
                'location.metroRecord',
                'author.userProfile',
                'agent.userProfile',
            ])
            ->byId($id)
            ->groupBy('id');

        if ($model = $query->one()) {
            return $model;
        }

        throw new NotFoundHttpException('Complex not found');
    }
}