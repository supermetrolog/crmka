<?php

namespace app\usecases\TaskFavorite;

use app\dto\TaskFavorite\TaskFavoriteChangePositionDto;
use app\dto\TaskFavorite\TaskFavoriteDto;
use app\exceptions\services\TaskFavoriteAlreadyExistsException;
use app\helpers\ArrayHelper;
use app\kernel\common\database\interfaces\transaction\TransactionBeginnerInterface;
use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\kernel\common\models\exceptions\SaveModelException;
use app\models\TaskFavorite;
use app\repositories\TaskFavoriteRepository;
use Throwable;
use yii\base\InvalidArgumentException;
use yii\db\StaleObjectException;

class TaskFavoriteService
{
	private TaskFavoriteRepository       $taskFavoriteRepository;
	private TransactionBeginnerInterface $transactionBeginner;

	public function __construct(TaskFavoriteRepository $taskFavoriteRepository, TransactionBeginnerInterface $transactionBeginner)
	{
		$this->taskFavoriteRepository = $taskFavoriteRepository;
		$this->transactionBeginner    = $transactionBeginner;
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	public function create(TaskFavoriteDto $dto): TaskFavorite
	{
		if ($this->taskFavoriteRepository->existsByTaskIdAndUserId($dto->task_id, $dto->user_id)) {
			throw new TaskFavoriteAlreadyExistsException();
		}

		$transaction = $this->transactionBeginner->begin();

		try {
			$oldTopModel = $this->taskFavoriteRepository->findTopByUserId($dto->user_id);

			$model = new TaskFavorite(
				[
					'task_id' => $dto->task_id,
					'user_id' => $dto->user_id
				]
			);
			$model->saveOrThrow();

			$this->updateNextModelLink($oldTopModel, $model->id);

			$transaction->commit();

			return $model;
		} catch (Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws Throwable
	 * @throws StaleObjectException
	 */
	public function delete(TaskFavorite $model): void
	{
		$transaction = $this->transactionBeginner->begin();

		try {
			$nextModel = $this->taskFavoriteRepository->findByPrevId($model->id);
			$this->updateNextModelLink($nextModel, $model->prev_id);

			$model->delete();

			$transaction->commit();
		} catch (Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function getById(int $id): TaskFavorite
	{
		return $this->taskFavoriteRepository->findOneOrThrow($id);
	}

	/**
	 * @return TaskFavorite[]
	 */
	public function getAllSortedByUserId(int $userId): array
	{
		$models = $this->taskFavoriteRepository->findAllByUserId($userId);
		if (ArrayHelper::empty($models)) {
			return [];
		}

		return $this->getSortedModels($models);
	}

	/**
	 * @param TaskFavorite[] $models
	 *
	 * @return TaskFavorite[]
	 */
	private function getSortedModels(array $models): array
	{
		$currentModel = ArrayHelper::find($models, static fn(TaskFavorite $model) => is_null($model->prev_id));
		$sortedModels = [$currentModel];

		$linkedModelsByPrevId = [];
		foreach ($models as $model) {
			if (!is_null($model->prev_id)) {
				$linkedModelsByPrevId[$model->prev_id] = $model;
			}
		}

		while (ArrayHelper::keyExists($linkedModelsByPrevId, $currentModel->id)) {
			$currentModel   = $linkedModelsByPrevId[$currentModel->id];
			$sortedModels[] = $currentModel;
		}

		return $sortedModels;
	}

	/**
	 * @throws Throwable
	 */
	public function changePosition(int $id, TaskFavoriteChangePositionDto $dto): void
	{
		$transaction = $this->transactionBeginner->begin();

		try {
			$positionModel = $this->taskFavoriteRepository->findOneOrThrow($id);

			$nextCurrentPositionModel = $this->taskFavoriteRepository->findByPrevId($positionModel->id);
			$this->updateNextModelLink($nextCurrentPositionModel, $positionModel->prev_id);

			if (!$this->isCorrectOrder($dto->prev_id, $dto->next_id)) {
				throw new InvalidArgumentException();
			}

			$this->updatePositionModelLink($positionModel, $dto);

			$transaction->commit();
		} catch (Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}

	/**
	 * @throws ModelNotFoundException
	 */
	private function isCorrectOrder(?int $prevId, ?int $nextId): bool
	{
		if (!is_null($nextId)) {
			$nextUpdatedPositionModel = $this->taskFavoriteRepository->findOneOrThrow($nextId);

			return $nextUpdatedPositionModel->prev_id === $prevId;
		}

		$maybeNextModel = $this->taskFavoriteRepository->findByPrevId($prevId);

		return is_null($maybeNextModel);
	}

	/**
	 * @throws SaveModelException
	 * @throws Throwable
	 */
	private function updatePositionModelLink(TaskFavorite $positionModel, TaskFavoriteChangePositionDto $dto): void
	{
		$transaction = $this->transactionBeginner->begin();

		try {
			if (!is_null($dto->next_id)) {
				$nextUpdatedPositionModel = $this->taskFavoriteRepository->findOneOrThrow($dto->next_id);

				$positionModel->prev_id            = $nextUpdatedPositionModel->prev_id;
				$nextUpdatedPositionModel->prev_id = $positionModel->id;

				$nextUpdatedPositionModel->saveOrThrow();
			} else {
				$prevUpdatedPositionModel = $this->taskFavoriteRepository->findOneOrThrow($dto->prev_id);
				$positionModel->prev_id   = $prevUpdatedPositionModel->id;
			}

			$positionModel->saveOrThrow();

			$transaction->commit();
		} catch (Throwable $th) {
			$transaction->rollback();
			throw $th;
		}

	}

	/**
	 * @param int|null $id It can be either model id or model previous id, depending on the processing logic.
	 *
	 * @throws SaveModelException
	 */
	private function updateNextModelLink(?TaskFavorite $nextModel, ?int $id): void
	{
		if (!is_null($nextModel)) {
			$nextModel->prev_id = $id;
			$nextModel->saveOrThrow();
		}
	}
}