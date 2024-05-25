<?php

declare(strict_types=1);

namespace app\repositories;

use app\kernel\common\models\exceptions\ModelNotFoundException;
use app\models\Reminder;

class ReminderRepository
{
	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedBy(int $id, int $createdById, string $createdByType): Reminder
	{
		return Reminder::find()
		           ->byId($id)
		           ->notDeleted()
		           ->byMorph($createdById, $createdByType)
		           ->oneOrThrow();
	}

	/**
	 * @throws ModelNotFoundException
	 */
	public function findModelByIdAndCreatedByOrUserId(int $id, int $userId, string $createdByType): Reminder
	{
		return Reminder::find()
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
		/** @var array */
		$result = Reminder::find()->select([
			'SUM(IF(status='.Reminder::STATUS_CREATED.', 1, 0)) AS created',
			'SUM(IF(status='.Reminder::STATUS_ACCEPTED.', 1, 0)) AS accepted',
			'SUM(IF(status='.Reminder::STATUS_DONE.', 1, 0)) AS done',
			'SUM(IF(status='.Reminder::STATUS_IMPOSSIBLE.', 1, 0)) AS impossible',
			'SUM(IF(status='.Reminder::STATUS_LATER.', 1, 0)) AS later',
		])->filterWhere(['user_id' => $user_id])->notDeleted()->asArray()->all()[0];
		
		$result = array_map(fn($value) => (int)$value, $result);

		return $result;
	}
}