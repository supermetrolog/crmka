<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\TaskComment;

class TaskCommentRepository
{
	/**
	 * Поиск всех комментариев для определенной задачи
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public function findAllByTaskId(int $id): array
	{
		return TaskComment::find()->andWhere(['task_id' => $id])->notDeleted()->all();
	}
}