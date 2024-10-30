<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Productrange;
use yii\base\ErrorException;

class ProductRangeRepository
{
	/**
	 * @return string[]
	 * @throws ErrorException
	 */
	public function getUniqueAll(): array
	{
		return Productrange::find()->distinct()->select(Productrange::field('product'))->column();
	}
}