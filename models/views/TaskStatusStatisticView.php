<?php

namespace app\models\views;

use app\models\Task;
use app\models\TaskObserver;
use yii\db\Expression;

/**
 * This is the model class for task statistic for table "task".
 *
 * @property ?int $total
 * @property ?int $created
 * @property ?int $accepted
 * @property ?int $done
 * @property ?int $impossible
 *
 */
class TaskStatusStatisticView extends Task
{
	public ?int $total      = null;
	public ?int $created    = null;
	public ?int $accepted   = null;
	public ?int $done       = null;
	public ?int $impossible = null;

	public function getObservers(): \yii\db\ActiveQuery
	{
		$query = $this->hasMany(TaskObserver::class, ['task_id' => 'id']);
		$query->andWhere(['or',
		                  [TaskObserver::getColumn('id') => null],
		                  ['<>', TaskObserver::getColumn('user_id'), new Expression(TaskStatusStatisticView::getColumn('user_id'))]]);

		return $query;
	}
}

