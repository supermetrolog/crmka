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
        $query = Complex::find()
            ->with([
                'objects.company.consultant.userProfile',
                'objects.objectClassRecord',
                'objects.firefightingType',
                'objects.location.regionRecord',
                'location.regionRecord',
                'location.highwayRecord',
                'location.directionRecord',
                'location.districtRecord',
                'location.districtMoscowRecord',
                'location.townRecord.townTypeRecord',
                'location.townCentralRecord',
                'location.metroRecord',
                'location.districtTypeRecord',
                'author.userProfile',
                'agent.userProfile',
                'objects.floorsRecords.number',
                'objects.floorsRecords.parts',
                'objects.cranes.state',
                'objects.cranes.beam',
                'objects.cranes.beamAmount',
                'objects.cranes.hoisting',
                'objects.cranes.location',
                'objects.cranes.type',
                'objects.commercialOffers.dealTypeRecord',
                'objects.commercialOffers.companyRecord.consultant.userProfile',
                'objects.commercialOffers.companyRecord.mainContact',
                'objects.commercialOffers.blocks',
            ])
            ->byId($id)
            ->groupBy('id');

        if ($model = $query->one()) {
            return $model;
        }

        throw new NotFoundHttpException('Complex not found');
    }


objects.commercialOffers.companyRecord.consultant.userProfile,
objects.commercialOffers.companyRecord.mainContact,
objects.commercialOffers.blocks,
objects.commercialOffers.blocks.partsRecords.floor.number

}