<?php

declare(strict_types=1);

namespace app\usecases\SurveyAction;

use app\dto\SurveyAction\CreateSurveyActionDto;
use app\dto\SurveyAction\UpdateSurveyActionDto;
use app\enum\Survey\SurveyActionStatusEnum;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\SurveyAction;
use Throwable;

class SurveyActionService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateSurveyActionDto $dto): SurveyAction
	{
		$action = new SurveyAction([
			'survey_id'     => $dto->survey->id,
			'type'          => $dto->type,
			'target_id'     => $dto->target_id,
			'status'        => $dto->status ?? SurveyActionStatusEnum::DONE,
			'comment'       => $dto->comment,
			'completed_at'  => $dto->completed_at,
			'created_by_id' => $dto->createdBy->id,
		]);

		$action->saveOrThrow();

		return $action;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(SurveyAction $model, UpdateSurveyActionDto $dto): SurveyAction
	{
		$model->load([
			'target_id'    => $dto->target_id,
			'status'       => $dto->status,
			'comment'      => $dto->comment,
			'completed_at' => $dto->completed_at
		]);

		$model->saveOrThrow();

		return $model;
	}
}