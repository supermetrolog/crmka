<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\Deal;

class DealRepository extends AbstractRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Deal
	{
		return Deal::find()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?Deal
	{
		return Deal::find()->byId($id)->one();
	}

	public function findAll(): array
	{
		return Deal::find()->all();
	}
}