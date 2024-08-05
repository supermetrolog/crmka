<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\CreateTaskDto;
use app\dto\Task\CreateTaskForUsersDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use Throwable;

class CreateTaskService
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
	 */
	public function create(CreateTaskDto $dto): Task
	{
		$task = new Task([
			'user_id'         => $dto->user->id,
			'message'         => $dto->message,
			'status'          => $dto->status,
			'start'           => $dto->start ? $dto->start->format('Y-m-d H:i:s') : null,
			'end'             => $dto->end ? $dto->end->format('Y-m-d H:i:s') : null,
			'created_by_type' => $dto->created_by_type,
			'created_by_id'   => $dto->created_by_id,
		]);

		$task->saveOrThrow();

		return $task;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function createForUsers(CreateTaskForUsersDto $dto): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$tasks = [];

			foreach ($dto->users as $user) {
				$tasks[] = $this->create(new CreateTaskDto([
					'user'            => $user,
					'message'         => $dto->message,
					'status'          => $dto->status,
					'start'           => $dto->start,
					'end'             => $dto->end,
					'created_by_type' => $dto->created_by_type,
					'created_by_id'   => $dto->created_by_id,
				]));
			}

			$tx->commit();

			return $tasks;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}