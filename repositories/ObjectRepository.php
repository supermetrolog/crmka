<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Objects;
use yii\db\ActiveRecord;

class ObjectRepository
{
	/**
	 * @return ?ActiveRecord|Objects
	 */
	public function findOne(int $id): ?ActiveRecord
	{
		return Objects::find()
		              ->byId($id)
		              ->one();
	}

	/**
	 * * @return ActiveRecord|Objects
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): ActiveRecord
	{
		return Objects::find()
		              ->byId($id)
		              ->oneOrThrow();
	}
}