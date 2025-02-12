<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Survey;

class SurveyRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): Survey
	{
		return Survey::find()->byId($id)->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneByIdWithRelationsOrThrow(int $id): Survey
	{
		return Survey::find()
		             ->byId($id)
		             ->with(['tasks.user.userProfile',
		                     'tasks.tags',
		                     'tasks.createdByUser.userProfile',
		                     'tasks.observers.user.userProfile',
		                     'tasks.targetUserObserver'])
		             ->oneOrThrow();
	}
}