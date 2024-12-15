<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\TaskHistory;
use app\models\views\TaskHistoryView;

class TaskHistoryRepository
{
	public function findLastByTaskId(int $taskId): ?TaskHistory
	{
		return TaskHistory::find()->byTaskId($taskId)->orderBy(['id' => SORT_DESC])->one();
	}

	/* @return TaskHistoryView[] */
	public function findViewsByTaskId(int $taskId): array
	{
		return TaskHistoryView::find()
		                      ->byTaskId($taskId)
		                      ->with(['user.userProfile', 'createdByUser.userProfile', 'events'])
		                      ->all();
	}
}