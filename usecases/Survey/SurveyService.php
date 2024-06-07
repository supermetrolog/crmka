<?php

declare(strict_types=1);

namespace app\usecases\Survey;

use app\dto\Survey\CreateSurveyDto;
use app\dto\Survey\UpdateSurveyDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Survey;
use Throwable;
use yii\db\StaleObjectException;

class SurveyService
{
	/**
	 * @throws SaveModelException
	 */
	public function create(CreateSurveyDto $dto): Survey
	{
		$model = new Survey([
			'user_id'    => $dto->user->id,
			'contact_id' => $dto->contact->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws SaveModelException
	 */
	public function update(Survey $model, UpdateSurveyDto $dto): Survey
	{
		$model->load([
			'user_id'    => $dto->user->id,
			'contact_id' => $dto->contact->id,
		]);

		$model->saveOrThrow();

		return $model;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(Survey $model): void
	{
		$model->delete();
	}
}