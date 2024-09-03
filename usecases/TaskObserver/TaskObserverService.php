<?php

declare(strict_types=1);

namespace app\usecases\TaskObserver;

use app\dto\TaskObserver\CreateTaskObserverDto;
use app\helpers\DateTimeHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskObserver;
use Throwable;

class TaskObserverService
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
	public function create(CreateTaskObserverDto $dto): TaskObserver
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$observer = new TaskObserver([
				'task_id'       => $dto->task_id,
				'user_id'       => $dto->user_id,
				'created_by_id' => $dto->created_by_id,
			]);

			$observer->saveOrThrow();
			$tx->commit();

			return $observer;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}

	/**
	 * @throws SaveModelException
	 */
	public function observe(TaskObserver $observer): TaskObserver
	{
		$observer->viewed_at = DateTimeHelper::nowf();
		$observer->saveOrThrow();

		return $observer;
	}

	/**
	 * @throws Throwable
	 */
	public function delete(TaskObserver $observer): void
	{
		$observer->delete();
	}

	public function deleteAll($condition): void
	{
		TaskObserver::deleteAll($condition);
	}
}