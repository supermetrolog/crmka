<?php

declare(strict_types=1);

namespace app\usecases\QuestionAnswer;

use app\dto\QuestionAnswer\CreateQuestionAnswerDto;
use app\dto\QuestionAnswer\UpdateQuestionAnswerDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\QuestionAnswer;
use Throwable;
use yii\db\StaleObjectException;

class QuestionAnswerService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateQuestionAnswerDto $dto): QuestionAnswer
	{
		$model = new QuestionAnswer([
			'question_id' => $dto->question_id,
			'field_id'    => $dto->field_id,
			'category'    => $dto->category,
			'value'       => $dto->value,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(QuestionAnswer $model, UpdateQuestionAnswerDto $dto): QuestionAnswer
	{
		$model->load([
			'question_id' => $dto->question_id,
			'field_id'    => $dto->field_id,
			'category'    => $dto->category,
			'value'       => $dto->value,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(QuestionAnswer $model): void
	{
		$model->delete();
	}
}