<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Task;

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

	public function getStatusStatisticByUserId(array $data = []): array
	{
		$user_id = $data['user_id'] ?? null;
		$start_date = $data['start_date'] ?? null;
		$end_date = $data['end_date'] ?? null;

		/** @var array */
		$statuses = Task::find()->select([
			'SUM(IF(status='.Task::STATUS_CREATED.', 1, 0)) AS created',
			'SUM(IF(status='.Task::STATUS_ACCEPTED.', 1, 0)) AS accepted',
			'SUM(IF(status='.Task::STATUS_DONE.', 1, 0)) AS done',
			'SUM(IF(status='.Task::STATUS_IMPOSSIBLE.', 1, 0)) AS impossible',
			'SUM(IF(end<=CURRENT_TIMESTAMP, 1, 0)) AS expired',
			'SUM(1) AS all',
		])
		->filterWhere(['user_id' => $user_id])
		->andFilterCompare('UNIX_TIMESTAMP(created_at)', $start_date, '>=')
		->andFilterCompare('UNIX_TIMESTAMP(created_at)', $end_date, '<=')
		->notDeleted()->asArray()->all()[0];

		$statuses = array_map(fn($value) => (int)$value, $statuses);
		
		return $statuses;
	}
}