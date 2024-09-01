<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\dto\Task\CreateTaskDto;
use app\dto\Task\CreateTaskForUsersDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\usecases\TaskObserver\TaskObserverService;
use Throwable;

class CreateTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskObserverService          $taskObserverService;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskObserverService $taskObserverService
	)
	{
		$this->transactionBeginner = $transactionBeginner;
		$this->taskObserverService = $taskObserverService;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTaskDto $dto): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
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
			$task->linkManyToManyRelations('tags', $dto->tagIds);

			$this->taskObserverService->create(new CreateTaskObserverDto([
				'task_id'       => $task->id,
				'user_id'       => $dto->user->id,
				'created_by_id' => $dto->created_by_id,
			]));

			foreach ($dto->observerIds as $observerId) {
				$this->taskObserverService->create(new CreateTaskObserverDto([
					'task_id'       => $task->id,
					'user_id'       => $observerId,
					'created_by_id' => $dto->created_by_id,
				]));
			}

			$tx->commit();

			return $task;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
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
				$task = $this->create(new CreateTaskDto([
					'user'            => $user,
					'message'         => $dto->message,
					'status'          => $dto->status,
					'start'           => $dto->start,
					'end'             => $dto->end,
					'created_by_type' => $dto->created_by_type,
					'created_by_id'   => $dto->created_by_id,
					'tagIds'          => $dto->tagIds,
					'observerIds'     => $dto->observerIds
				]));

				$tasks[] = $task;
			}

			$tx->commit();

			return $tasks;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}