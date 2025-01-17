<?php

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\TaskFavorite;

class TaskFavoriteRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findOneOrThrow(int $id): TaskFavorite
	{
		return TaskFavorite::find()->notDeleted()->byId($id)->oneOrThrow();
	}

	public function findOne(int $id): ?TaskFavorite
	{
		return TaskFavorite::find()->notDeleted()->byId($id)->one();
	}

	public function existsByTaskIdAndUserId(int $taskId, int $userId): bool
	{
		return TaskFavorite::find()->notDeleted()->byUserId($userId)->byTaskId($taskId)->exists();
	}

	public function findTopByUserId(int $userId): ?TaskFavorite
	{
		return TaskFavorite::find()->notDeleted()->byUserId($userId)->top()->one();

	}

	public function findByPrevId(int $prevId): ?TaskFavorite
	{
		return TaskFavorite::find()->notDeleted()->byPrevId($prevId)->one();
	}

	/**
	 * @return TaskFavorite[]
	 */
	public function findAllByUserId(int $userId): array
	{
		return TaskFavorite::find()->with([
			'task.user.userProfile',
			'task.tags',
			'task.createdByUser.userProfile',
			'task.observers.user.userProfile',
			'task.targetUserObserver'
		])->notDeleted()->byUserId($userId)->all();
	}
}