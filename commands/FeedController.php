<?php

namespace app\commands;

use app\components\avito\AvitoFeedGenerator;
use app\components\connector\avito\AvitoConnector;
use app\models\OfferMix;
use DOMException;
use yii\console\Controller;

class FeedController extends Controller
{
    private AvitoFeedGenerator $avitoFeedGenerator;

    public function init()
    {
        parent::init();
        $this->avitoFeedGenerator = new AvitoFeedGenerator();
    }

    /**
     * @return void
     * @throws DOMException
     */
    public function actionAvito(): void
    {
        $models = OfferMix::find()
            ->limit(2)
            ->notDelete()
            ->active()
            ->offersType()
            ->all();

        $connector = new AvitoConnector($models);

        $this->avitoFeedGenerator->setAvitoObjects($connector->getData());

        $res = $this->avitoFeedGenerator->generate();

        echo $res;
    }
}