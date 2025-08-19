<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\LetterContactAnswer;

class LetterContactAnswerRepository extends AbstractRepository
{
	public function findOne(int $id): ?LetterContactAnswer
	{
		/** @var ?LetterContactAnswer */
		return LetterContactAnswer::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): LetterContactAnswer
	{
		/** @var LetterContactAnswer */
		return LetterContactAnswer::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return LetterContactAnswer[]
	 */
	public function findAll(): array
	{
		return LetterContactAnswer::find()->all();
	}
}