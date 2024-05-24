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
}