<?php

namespace app\models\views;

use app\models\Task;

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
}
