<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\CreateTaskCommentDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskComment;
use Throwable;

class CreateTaskCommentService
{
	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTaskCommentDto $dto): TaskComment
	{
		$comment = new TaskComment([
			'message'       => $dto->message,
			'created_by_id' => $dto->created_by_id,
			'task_id'       => $dto->task_id
		]);

		$comment->saveOrThrow();
		
		$comment->refresh();

		return $comment;
	}
}