<?php

namespace app\models\views;

use app\models\Task;
use app\models\TaskObserver;
use yii\base\ErrorException;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for statistic for table "task".
 *
 * @property int $by_created_by
 * @property int $by_user
 * @property int $by_observer
 */
class TaskRelationStatisticView extends Task
{
	public ?int $by_created_by = null;
	public ?int $by_user       = null;
	public ?int $by_observer   = null;


	/**
	 * @throws ErrorException
	 */
	public function getObservers(): ActiveQuery
	{
		$query = $this->hasMany(TaskObserver::class, ['task_id' => 'id']);
		$query->andWhere(['or',
		                  [TaskObserver::getColumn('id') => null],
		                  ['<>', TaskObserver::getColumn('user_id'), new Expression(TaskRelationStatisticView::getColumn('user_id'))]]);

		return $query;
	}
}
