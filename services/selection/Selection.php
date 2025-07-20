<?php

namespace app\services\selection;

use app\enum\Request\RequestStatusEnum;
use app\events\NotificationEvent;
use app\models\miniModels\TimelineStep;
use app\models\Notification;
use app\models\oldDb\OfferMixSearch;
use app\models\Request;
use app\models\ServiceSelection;
use Yii;
use yii\base\Model;

class Selection extends Model
{
	public const NEW_RECOMMENDED_OFFERS = 'new_recommended_offers';

	public function init()
	{
		$this->on(self::NEW_RECOMMENDED_OFFERS, [Yii::$app->notify, 'notifyUser']);
		parent::init();
	}

	public function run()
	{
		$requests = Request::find()
		                   ->distinct()
		                   ->joinWith(['timelines.timelineSteps'])
		                   ->with(['regions', 'gateTypes', 'directions', 'districts'])
		                   ->where([
			                   'timeline_step.negative' => TimelineStep::IS_NEGATIVE,
			                   'request.status'         => RequestStatusEnum::ACTIVE,
		                   ])
		                   ->orderBy(['updated_at' => SORT_DESC])
		                   ->all();


		foreach ($requests as $request) {
			$this->processedRequest($request);
		}
	}


	private function processedRequest($request)
	{
		$offersSearchModel = new OfferMixSearch();

		$query = $this->generateQuery($request);

		$offers = $offersSearchModel->search($query);
		$offers->setPagination([
			'defaultPageSize' => 0,
			'pageSizeLimit'   => [0, 50],
		]);
		$recommended = $offers->getModels();

		$count = $offers->getPagination()->totalCount;

		$selection = ServiceSelection::find()
		                             ->where(['request_id' => $request->id])
		                             ->limit(1)
		                             ->one();

		if (!$selection) {
			return ServiceSelection::createSelection([
				'request_id'               => $request->id,
				'recommended_offers_count' => $count
			]);
		}
		$diff = $count - $selection->recommended_offers_count;
		if ($diff > 0) {
			$newRecommendedOffersQueryString = $this->toQueryString($this->getNewRecommendedOffers($recommended, $diff));
			$this->trigger(self::NEW_RECOMMENDED_OFFERS, new NotificationEvent([
				'consultant_id' => $request->consultant_id,
				'type'          => Notification::TYPE_COLLECTION_INFO,
				'title'         => 'подборка',
				'body'          => Yii::$app->controller->renderFile('@app/views/notifications_template/new_collection_offers.php', [
					'model'                           => $request,
					'count'                           => $diff,
					'newRecommendedOffersQueryString' => $newRecommendedOffersQueryString
				])
			]));
		}

		$selection->recommended_offers_count = $count;
		$selection->save();
	}

	private function toQueryString($recommended)
	{
		$array = [];

		foreach ($recommended as $value) {
			$array[] = "new_original_id=" . $value->original_id;
		}

		return implode('&', $array);
	}

	private function getNewRecommendedOffers($recommended, $diff)
	{
		$count = count($recommended) - 1;
		$array = [];
		for ($i = $count; $i > $count - $diff; $i--) {
			$array[] = $recommended[$i];
		}

		return $array;
	}

	private function generateQuery($request)
	{
		$query = [
			'type_id'                     => implode(",", [1, 2]),
			'page'                        => 1,
			'per-page'                    => 20,
			'rangeMinElectricity'         => $request->electricity,
			'approximateDistanceFromMKAD' => $request->distanceFromMKAD,
			'deal_type'                   => (string)$request->dealType,
			'rangeMaxArea'                => $request->maxArea,
			'rangeMinArea'                => $request->minArea,
			'rangeMaxPricePerFloor'       => $request->pricePerFloor,
			'rangeMinCeilingHeight'       => $request->minCeilingHeight,
			'rangeMaxCeilingHeight'       => $request->maxCeilingHeight,
			'heated'                      => $request->heated === 0 ? 2 : $request->heated,
			'has_cranes'                  => $request->haveCranes,
			'floor_types'                 => $request->antiDustOnly ? implode(",", [2]) : "",
			'region'                      => implode(",", array_map(function ($item) {
				return $item->region;
			}, $request->regions)),
			'status'                      => 1,
			'gates'                       => implode(",", array_map(function ($item) {
				return $item->gate_type;
			}, $request->gateTypes)),
			'direction'                   => implode(",", array_map(function ($item) {
				return $item->direction;
			}, $request->directions)),
			'district_moscow'             => implode(",", array_map(function ($item) {
				return $item->district;
			}, $request->districts)),
			'firstFloorOnly'              => $request->firstFloorOnly ? 1 : null,
			'recommended_sort'            => null,
		];

		return $this->removeZeroValue($query);
	}

	private function removeZeroValue(array $array): array
	{
		foreach ($array as $key => $value) {
			if ($value === null || $value === "" || (is_array($value) && count($value) == 0)) {
				unset($array[$key]);
			}
		}

		return $array;
	}
}
