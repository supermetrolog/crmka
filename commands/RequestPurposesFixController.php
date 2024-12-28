<?php

declare(strict_types=1);

namespace app\commands;

use app\kernel\common\controller\ConsoleController;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\miniModels\RequestObjectType;
use app\models\oldDb\OfferMix;
use app\models\Request;
use yii\base\ErrorException;

class RequestPurposesFixController extends ConsoleController
{
	/**
	 * @throws ErrorException
	 * @throws SaveModelException
	 */
	public function actionIndex(): void
	{
		$query = Request::find()
		                ->joinWith('objectTypes')
		                ->orWhere(['<', Request::field('created_at'), '2024-01-01 00:00:00'])
		                ->where([RequestObjectType::field('object_type') => 0])
		                ->distinct();

		$totalCount = $query->count();

		/** @var Request $request */
		foreach ($query->each(1000) as $request) {
			$isUpdated = false;

			/** @var RequestObjectType $objectType */
			foreach ($request->objectTypes as $objectType) {
				$correctObjectType = OfferMix::normalizeObjectTypes($objectType->object_type);

				if ($correctObjectType !== $objectType->object_type && !is_null($correctObjectType)) {
					$isUpdated = true;

					$objectType->object_type = $correctObjectType;
					$objectType->saveOrThrow();
				}
			}

			if ($isUpdated) {
				$this->infof('Updated purposes for request with ID: %d', $request->id);
			}
		}

		$this->infof('Complete. Updated %d requests', $totalCount);
	}
}