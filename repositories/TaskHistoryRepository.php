<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\TaskHistory;

class TaskHistoryRepository
{
	public function findLastByTaskId(int $taskId): ?TaskHistory
	{
		return TaskHistory::find()->byTaskId($taskId)->orderBy(['id' => SORT_DESC])->one();
	}

	/* @return TaskHistory[] */
	public function findByTaskId(int $taskId): array
	{
		return TaskHistory::find()
		                  ->byTaskId($taskId)
		                  ->with(['user', 'createdByUser', 'events'])
		                  ->all();
	}
}