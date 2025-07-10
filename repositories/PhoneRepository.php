<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\miniModels\Phone;

class PhoneRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Phone
	{
		return Phone::find()->byId($id)->oneOrThrow();
	}
}