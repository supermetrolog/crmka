<?php

declare(strict_types=1);

namespace app\actions\Company;

use app\helpers\StringHelper;
use app\kernel\common\actions\Action;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Productrange;

class FixCompanyProductRangesAction extends Action
{
	/**
	 * @throws SaveModelException
	 */
	public function run(): void
	{
		$this->info('Start fix product ranges');

		$query = Productrange::find();

		$changedProductRangesCount = 0;

		/** @var Productrange $productRange */
		foreach ($query->each(200) as $productRange) {
			$correctProductRange = StringHelper::trim($productRange->product);


			if (!StringHelper::isAbbreviation(StringHelper::toWords($correctProductRange)[0])) {
				$correctProductRange = StringHelper::lcFirst($correctProductRange);
			}

			if ($correctProductRange !== $productRange->product && StringHelper::isNotEmpty($correctProductRange)) {
				$productRange->product = $correctProductRange;
				$productRange->saveOrThrow(false);

				$changedProductRangesCount++;
			}
		}

		$this->infof('Complete. Edited product ranges: %d', $changedProductRangesCount);
	}
}