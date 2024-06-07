<?php

declare(strict_types=1);

namespace app\usecases\Question;

use app\dto\Question\CreateQuestionDto;
use app\dto\Question\UpdateQuestionDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Question;
use Throwable;
use yii\db\StaleObjectException;

class QuestionService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateQuestionDto $dto): Question
	{
		$model = new Question([
			'text' => $dto->text,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Question $model, UpdateQuestionDto $dto): Question
	{
		$model->load([
			'text' => $dto->text,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Question $model): void
	{
		$model->delete();
	}
}