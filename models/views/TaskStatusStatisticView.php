<?php

namespace app\models\views;

use app\models\Task;

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
}

