<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\TaskComment\UpdateTaskCommentDto;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskComment;
use Exception;
use Throwable;
use yii\db\StaleObjectException;

class TaskCommentService
{

	/**
	 * @throws SaveModelException
	 * @throws Exception
	 */
	public function update(TaskComment $comment, UpdateTaskCommentDto $dto): TaskComment
	{
		$comment->load([
			'message' => $dto->message,
		]);

		$comment->saveOrThrow();

		return $comment;
	}

	/**
	 * @throws StaleObjectException
	 * @throws Throwable
	 */
	public function delete(TaskComment $comment): void
	{
		$comment->delete();
	}
}