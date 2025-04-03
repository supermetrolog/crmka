<?php

declare(strict_types=1);

namespace app\usecases\Object;

use app\helpers\ArrayHelper;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Objects;
use InvalidArgumentException;
use yii\helpers\Json;

class ObjectService
{
	/**
	 * @throws SaveModelException
	 */
	public function fixLandObjectPurposes(Objects $object, array $purposes = ["9", "14", "15", "26", "31", "32"]): void
	{
		if (!$object->isLand()) {
			throw new InvalidArgumentException('Object is not land');
		}

		if (ArrayHelper::notEmpty($object->getPurposes())) {
			throw new InvalidArgumentException('Object purposes are not empty');
		}

		$object->purposes = Json::encode($purposes);

		$object->saveOrThrow(false);
	}
}