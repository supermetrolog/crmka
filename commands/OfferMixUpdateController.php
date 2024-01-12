<?php

declare(strict_types=1);

namespace app\commands;

use app\models\OfferMix;
use yii\console\Controller;

class OfferMixUpdateController extends Controller
{
    public function actionIndex(): void
    {
        $query = OfferMix::find()->with(['miniOffersMix'])->generalType()->active()->notDelete();

        $batchCount = 0;

        /** @var OfferMix[] $offers */
        foreach ($query->batch(500) as $offers) {
            $batchCount++;

            echo $batchCount . PHP_EOL;

            foreach ($offers as $offer) {
                if (!$offer->miniOffersMix) {
                    continue;
                }

                $offer->is_fake = max(array_map(function(\app\models\oldDb\OfferMix $model) {return $model->is_fake;}, $offer->miniOffersMix));
                $offer->save(false);
            }
        }
    }
}