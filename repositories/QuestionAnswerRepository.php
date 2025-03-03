<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\QuestionAnswer;

class QuestionAnswerRepository
{
	public function findOne(int $id): ?QuestionAnswer
	{
		return QuestionAnswer::find()->byId($id)->notDeleted()->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): QuestionAnswer
	{
		return QuestionAnswer::find()->byId($id)->notDeleted()->oneOrThrow();
	}
}
