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

	public function getStatusStatisticByUserId(int $user_id): array
	{
		$result = Task::find()->select('COUNT(*)')->where(['user_id' => $user_id])->byStatus(Task::STATUS_CREATED)->notDeleted()
				->union(
					Task::find()->select('COUNT(*)')->where(['user_id' => $user_id])->byStatus(Task::STATUS_ACCEPTED)->notDeleted(), true
				)->union(
					Task::find()->select('COUNT(*)')->where(['user_id' => $user_id])->byStatus(Task::STATUS_DONE)->notDeleted(), true
				)->union(
					Task::find()->select('COUNT(*)')->where(['user_id' => $user_id])->byStatus(Task::STATUS_IMPOSSIBLE)->notDeleted(), true
				)->column();

		return [
			'created' => $result[0],
			'accepted' => $result[1],
			'done' => $result[2],
			'impossible' => $result[3],
		];
	}
}