<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\ContactPosition;

class ContactPositionRepository extends AbstractRepository
{
	public function findOne(int $id): ?ContactPosition
	{
		/** @var ?ContactPosition */
		return ContactPosition::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): ContactPosition
	{
		/** @var ContactPosition */
		return ContactPosition::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return ContactPosition[]
	 */
	public function findAll(): array
	{
		return ContactPosition::find()->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC])->all();
	}
}