<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\CreateTaskCommentDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskComment;
use Throwable;

class CreateTaskCommentService
{
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner
	)
	{
		$this->transactionBeginner = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTaskCommentDto $dto): TaskComment
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$comment = new TaskComment([
				'message'       => $dto->message,
				'created_by_id' => $dto->created_by_id,
				'task_id'       => $dto->task_id
			]);

			$comment->saveOrThrow();
			$tx->commit();

			return $comment;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}