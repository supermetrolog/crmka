<?php

declare(strict_types=1);

namespace app\usecases\Task;

use app\components\EventManager;
use app\dto\Media\CreateMediaDto;
use app\dto\Task\CreateTaskDto;
use app\dto\Task\CreateTaskForUsersDto;
use app\dto\TaskObserver\CreateTaskObserverDto;
use app\events\Task\CreateTaskEvent;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\Survey;
use app\models\SurveyQuestionAnswer;
use app\models\Task;
use app\models\TaskObserver;
use app\repositories\UserRepository;
use app\usecases\TaskObserver\TaskObserverService;
use Throwable;

class CreateTaskService
{
	private TaskService                  $taskService;
	private TransactionBeginnerInterface $transactionBeginner;
	private TaskObserverService          $taskObserverService;
	private EventManager                 $eventManager;
	private UserRepository               $userRepository;

	public function __construct(
		TaskService $taskService,
		TransactionBeginnerInterface $transactionBeginner,
		TaskObserverService $taskObserverService,
		EventManager $eventManager,
		UserRepository $userRepository
	)
	{
		$this->taskService         = $taskService;
		$this->transactionBeginner = $transactionBeginner;
		$this->taskObserverService = $taskObserverService;
		$this->eventManager        = $eventManager;
		$this->userRepository      = $userRepository;
	}

	/**
	 * @param CreateMediaDto[] $mediaDtos
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(CreateTaskDto $dto, array $mediaDtos = []): Task
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$task = new Task([
				'user_id'         => $dto->user->id,
				'title'           => $dto->title,
				'message'         => $dto->message,
				'status'          => $dto->status,
				'start'           => $dto->start ? $dto->start->format('Y-m-d H:i:s') : null,
				'end'             => $dto->end ? $dto->end->format('Y-m-d H:i:s') : null,
				'created_by_type' => $dto->created_by_type,
				'created_by_id'   => $dto->created_by_id,
			]);

			$task->saveOrThrow();

			$task->linkManyToManyRelations('tags', $dto->tagIds);

			$this->taskService->linkRelationIfNeeded($task, SurveyQuestionAnswer::getMorphClass(), $dto->surveyQuestionAnswerId);
			$this->taskService->linkRelationIfNeeded($task, Survey::getMorphClass(), $dto->surveyId);

			$this->taskService->createFiles($task, $mediaDtos);

			$this->createObservers($task, $dto);

			$createdBy = $this->userRepository->findOne($dto->created_by_id);
			$this->eventManager->trigger(new CreateTaskEvent($task, $createdBy));

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
					'title'           => $dto->title,
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

	/**
	 * @return TaskObserver[]
	 *
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function createObservers(Task $task, CreateTaskDto $dto): array
	{
		$tx = $this->transactionBeginner->begin();

		try {
			$observers = [];

			$observer = $this->taskObserverService->create(new CreateTaskObserverDto([
				'task_id'       => $task->id,
				'user_id'       => $dto->user->id,
				'created_by_id' => $dto->created_by_id,
			]));

			if ($dto->user->id === $dto->created_by_id) {
				$this->taskObserverService->observe($observer);
			}

			$observers[] = $observer;

			foreach ($dto->observerIds as $observerId) {
				$observers[] = $this->taskObserverService->create(new CreateTaskObserverDto([
					'task_id'       => $task->id,
					'user_id'       => $observerId,
					'created_by_id' => $dto->created_by_id,
				]));
			}

			$tx->commit();

			return $observers;
		} catch (Throwable $th) {
			$tx->rollback();
			throw $th;
		}
	}
}