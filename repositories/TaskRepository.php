<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Task;
use app\models\TaskObserver;
use app\models\views\TaskRelationStatisticView;
use app\models\views\TaskStatusStatisticView;
use yii\base\ErrorException;
use yii\db\Expression;

class TaskRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedBy(int $id, int $createdById, string $createdByType): Task
	{
		return Task::find()
		           ->byId($id)
		           ->notDeleted()
		           ->byMorph($createdById, $createdByType)
		           ->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelById(int $id, bool $withDeleted = false): Task
	{
		$query = Task::find()->byId($id);

		if (!$withDeleted) {
			$query->notDeleted();
		}

		return $query->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedByOrUserId(int $id, int $userId, string $createdByType): Task
	{
		return Task::find()
		           ->byId($id)
		           ->notDeleted()
		           ->andWhere([
			           'OR',
			           ['user_id' => $userId],
			           [
				           'AND',
				           ['=', 'created_by_id', $userId],
				           ['=', 'created_by_type', $createdByType],
			           ]
		           ])
		           ->oneOrThrow();
	}

	public function getStatusStatisticByUserId(?int $user_id = null): array
	{
		return Task::find()->select([
			'SUM(IF(status=' . Task::STATUS_CREATED . ', 1, 0)) AS created',
			'SUM(IF(status=' . Task::STATUS_ACCEPTED . ', 1, 0)) AS accepted',
			'SUM(IF(status=' . Task::STATUS_DONE . ', 1, 0)) AS done',
			'SUM(IF(status=' . Task::STATUS_IMPOSSIBLE . ', 1, 0)) AS impossible',
		])->filterWhere(['user_id' => $user_id])->notDeleted()->asArray()->all();
	}

	/**
	 * @throws ErrorException
	 */
	public function getCountsStatistic(?int $user_id = null, ?int $created_by_id = null, ?int $observer_id = null): TaskStatusStatisticView
	{
		$subQuery          = TaskStatusStatisticView::find()->notDeleted();
		$tasksWithObserver = null;

		if ($observer_id) {
			$observerQuery = Task::find()->notDeleted()->joinWith('observers')->andWhere(
				['and',
				 [TaskObserver::getColumn('user_id') => $observer_id],
				 ['<>', new Expression(TaskObserver::getColumn('user_id')), new Expression(Task::getColumn('user_id'))]
				]
			);

			$observerQuery->select([
				Task::getColumn('id'),
			])->groupBy(Task::getColumn('id'));

			$tasksWithObserver = $observerQuery->column();

			if (empty($user_id) and empty($created_by_id)) {
				$subQuery->andWhere([Task::getColumn('id') => $tasksWithObserver]);
			}
		}

		$subQuery->andFilterWhere(['or',
		                           [TaskStatusStatisticView::getColumn('user_id') => $user_id],
		                           [TaskStatusStatisticView::getColumn('created_by_id') => $created_by_id],
		                           [Task::getColumn('id') => $tasksWithObserver]
		]);

		$query = TaskStatusStatisticView::find()->from($subQuery)->select([
			'total'      => 'COUNT(*)',
			'created'    => 'SUM(IF(status=' . Task::STATUS_CREATED . ', 1, 0))',
			'accepted'   => 'SUM(IF(status=' . Task::STATUS_ACCEPTED . ', 1, 0))',
			'done'       => 'SUM(IF(status=' . Task::STATUS_DONE . ', 1, 0))',
			'impossible' => 'SUM(IF(status=' . Task::STATUS_IMPOSSIBLE . ', 1, 0))',
		]);

		/** @var TaskStatusStatisticView $model */
		$model = $query->one();

		return $model;
	}

	/**
	 * @throws ErrorException
	 */
	public function getRelationsStatisticByUserId(int $user_id): TaskRelationStatisticView
	{
		$subQuery = TaskRelationStatisticView::find()->notDeleted();
		$subQuery->select([
			'created_by_id' => 'IF(' . TaskRelationStatisticView::field('created_by_id') . ' = ' . $user_id . ', 1, 0)',
			'user_id'       => 'IF(' . TaskRelationStatisticView::field('user_id') . ' = ' . $user_id . ', 1, 0)',
			'observer_id'   => 'IF(' . TaskObserver::field('user_id') . ' = ' . $user_id . ' and ' . Task::getColumn('user_id') . ' <> ' . $user_id . ', 1, 0)',
		]);
		$subQuery->joinWith('observers')->groupBy(TaskRelationStatisticView::getColumn('id'));
		$subQuery->andFilterWhere(['or',
		                           [TaskRelationStatisticView::getColumn('user_id') => $user_id],
		                           [TaskRelationStatisticView::getColumn('created_by_id') => $user_id],
		                           [TaskObserver::getColumn('user_id') => $user_id]
		]);

		$query = TaskRelationStatisticView::find()->from($subQuery)->select([
			'by_created_by' => 'SUM(created_by_id)',
			'by_user'       => 'SUM(user_id)',
			'by_observer'   => 'SUM(observer_id)',
		]);

		/** @var TaskRelationStatisticView $model */
		$model = $query->one();

		return $model;
	}
}