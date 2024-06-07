<?php

declare(strict_types=1);

namespace app\usecases\SurveyQuestionAnswer;

use app\dto\SurveyQuestionAnswer\CreateSurveyQuestionAnswerDto;
use app\dto\SurveyQuestionAnswer\UpdateSurveyQuestionAnswerDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\SurveyQuestionAnswer;
use Throwable;
use yii\db\StaleObjectException;

class SurveyQuestionAnswerService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyQuestionAnswerDto $dto): SurveyQuestionAnswer
	{
		$model = new SurveyQuestionAnswer([
			'question_answer_id' => $dto->question_answer->id,
			'survey_id'          => $dto->survey->id,
			'value'              => $dto->value,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(SurveyQuestionAnswer $model, UpdateSurveyQuestionAnswerDto $dto): SurveyQuestionAnswer
	{
		$model->load([
			'question_answer_id' => $dto->question_answer->id,
			'survey_id'          => $dto->survey->id,
			'value'              => $dto->value,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(SurveyQuestionAnswer $model): void
	{
		$model->delete();
	}
}