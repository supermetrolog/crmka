<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\letter\LetterContact;

class LetterContactRepository
{
	public function findOne(int $id): ?LetterContact
	{
		/** @var LetterContact */
		return LetterContact::findOne($id);
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): LetterContact
	{
		$model = LetterContact::findOne($id);

		if ($model === null) {
			throw new ModelNotFoundException('LetterContact not found');
		}

		return $model;
	}

	/**
	 * @return LetterContact[]
	 */
	public function findAll(): array
	{
		return LetterContact::find()->all();
	}
}