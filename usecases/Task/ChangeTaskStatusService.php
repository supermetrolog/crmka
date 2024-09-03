<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\ChangeTaskStatusDto;
use app\dto\Task\CreateTaskCommentDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\kernel\web\http\responses\SuccessResponse;
use app\models\Task;
use Throwable;

class ChangeTaskStatusService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskService                  $taskService;
	private CreateTaskCommentService     $createTaskCommentService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskService $taskService,
		CreateTaskCommentService $createTaskCommentService
	)
	{
		$this->transactionBeginner      = $transactionBeginner;
		$this->taskService              = $taskService;
		$this->createTaskCommentService = $createTaskCommentService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function changeStatus(Task $task, ChangeTaskStatusDto $dto): SuccessResponse
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$this->taskService->changeStatus($task, $dto);

			if ($dto->comment) {
				$this->createTaskCommentService->create(new CreateTaskCommentDto([
					'message'       => $dto->comment,
					'created_by_id' => $dto->changed_by_id,
					'task_id'       => $task->id
				]));
			}

			$tx->commit();

			return new SuccessResponse();
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}