<?php

namespace app\commands;

use app\components\avito\AvitoFeedGenerator;
use app\components\connector\avito\AvitoConnector;
use app\models\OfferMix;
use DateTime;
use DOMException;
use Yii;
use yii\base\ErrorException;
use yii\console\Controller;

class FeedController extends Controller
{
    private AvitoFeedGenerator $avitoFeedGenerator;

    /**
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->avitoFeedGenerator = new AvitoFeedGenerator();
    }

    /**
     * @return void
     * @throws DOMException
     * @throws ErrorException
     */
    public function actionAvito(): void
    {
        $currentHours = (new DateTime())->format('H');

        if ($currentHours < 10 || $currentHours >= 20) {
            return;
        }

        $models = OfferMix::find()
            ->distinct()
            ->notDelete()
            ->active()
            ->adAvito()
            ->blockType()
            ->notResponseStorageDealType()
            ->with(['block', 'offer', 'object'])
            ->all();

        $this->stdout("COUNT: " . count($models) . "\n");

        $connector = new AvitoConnector($models);

        $this->avitoFeedGenerator->setAvitoObjects($connector->getData());

        $feed = $this->avitoFeedGenerator->generate();

        $filename = Yii::getAlias('@web/feeds/avito.xml');
        $this->saveFeed($feed, $filename);
    }

    /**
     * @param string $feed
     * @param $filename
     * @return void
     * @throws ErrorException
     */
    private function saveFeed(string $feed, $filename): void
    {
        if (!file_put_contents($filename, $feed)) {
            throw new ErrorException('Save feed error');
        }
    }
}