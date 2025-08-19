<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\repository\AbstractRepository;
use app\models\SurveyAction;

class SurveyActionRepository extends AbstractRepository
{
	public function findOne(int $id): ?SurveyAction
	{
		/** @var ?SurveyAction */
		return SurveyAction::find()->byId($id)->one();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): SurveyAction
	{
		/** @var SurveyAction */
		return SurveyAction::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @return SurveyAction[]
	 */
	public function findAll(): array
	{
		return SurveyAction::find()->all();
	}
}