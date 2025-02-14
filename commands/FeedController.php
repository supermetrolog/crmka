<?php

namespace app\commands;

use app\components\avito\AvitoFeedGenerator;
use app\components\connector\avito\AvitoConnector;
use app\components\interfaces\OfferInterface;
use app\models\OfferMix;
use DateTime;
use DOMException;
use Yii;
use yii\base\ErrorException;
use app\kernel\common\controller\ConsoleController;

class FeedController extends ConsoleController
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

		/** @var OfferInterface[]|OfferMix[] $models */
		$models = OfferMix::find()
		                  ->distinct()
		                  ->notDelete()
		                  ->active()
		                  ->adAvito()
		                  ->blockType()
		                  ->notResponseStorageDealType()
		                  ->with(['block', 'offer', 'object', 'complex'])
		                  ->all();

		$this->infof("Offers count: %d", count($models));

		$this->comment("Generating data...");

		$connector = new AvitoConnector($models);
		$data      = $connector->getData();

		$this->success("Generated data");

		$this->avitoFeedGenerator->setAvitoObjects($data);

		$this->comment("Generating xml feed...");

		$feed = $this->avitoFeedGenerator->generate();

		$this->success("Generated xml feed");

		$this->comment("Saving file...");

		$filename = Yii::getAlias('@web/feeds/avito.xml');
		$this->saveFeed($feed, $filename);

		$this->success("Saved file");
	}

	/**
	 * @param string $feed
	 * @param        $filename
	 *
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