<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Task;
use app\models\TaskObserver;
use yii\base\ErrorException;

class TaskRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedBy(int $id, int $createdById, string $createdByType): Task
	{
		return Task::find()
		           ->byId($id)
		           ->notDeleted()
		           ->byMorph($createdById, $createdByType)
		           ->oneOrThrow();
	}

	/**
	 * @param int $id
	 *
	 * @return Task
	 * @throws ModelNotFoundException
	 */
	public function findModelById(int $id): Task
	{
		return Task::find()
		           ->byId($id)
		           ->notDeleted()
		           ->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedByOrUserId(int $id, int $userId, string $createdByType): Task
	{
		return Task::find()
		           ->byId($id)
		           ->notDeleted()
		           ->andWhere([
			           'OR',
			           ['user_id' => $userId],
			           [
				           'AND',
				           ['=', 'created_by_id', $userId],
				           ['=', 'created_by_type', $createdByType],
			           ]
		           ])
		           ->oneOrThrow();
	}

	public function getStatusStatisticByUserId(?int $user_id = null): array
	{
		return Task::find()->select([
			'SUM(IF(status='.Task::STATUS_CREATED.', 1, 0)) AS created',
			'SUM(IF(status='.Task::STATUS_ACCEPTED.', 1, 0)) AS accepted',
			'SUM(IF(status='.Task::STATUS_DONE.', 1, 0)) AS done',
			'SUM(IF(status='.Task::STATUS_IMPOSSIBLE.', 1, 0)) AS impossible',
		])->filterWhere(['user_id' => $user_id])->notDeleted()->asArray()->all();
	}
}