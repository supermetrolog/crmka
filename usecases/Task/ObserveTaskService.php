<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\events\Task\ObserveTaskEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Task;
use app\models\User;
use app\repositories\TaskObserverRepository;
use app\usecases\TaskObserver\TaskObserverService;
use Throwable;

class ObserveTaskService
{
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskObserverService          $taskObserverService;
	private EventManager                 $eventManager;
	private TaskObserverRepository       $taskObserverRepository;

	public function __construct(
		TransactionBeginnerInterface $transactionBeginner,
		TaskObserverService $taskObserverService,
		EventManager $eventManager,
		TaskObserverRepository $taskObserverRepository
	)
	{
		$this->transactionBeginner    = $transactionBeginner;
		$this->taskObserverService    = $taskObserverService;
		$this->eventManager           = $eventManager;
		$this->taskObserverRepository = $taskObserverRepository;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 * @throws ModelNotFoundException
	 */
	public function observe(Task $task, User $observerUser): void
	{
		$tx = $this->transactionBeginner->begin();

		$observer = $this->taskObserverRepository->findOneByTaskIdAndUserId($task->id, $observerUser->id);

		try {
			$this->taskObserverService->observe($observer);

			$this->eventManager->trigger(new ObserveTaskEvent($task, $observerUser));

			$tx->commit();
		} catch (Throwable $e) {
			$tx->rollBack();
			throw $e;
		}
	}
}