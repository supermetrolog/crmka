<?php

namespace app\models\views;

use app\models\TaskEvent;
use app\models\TaskHistory;

/**
 * @property TaskEvent[] $events
 */
class TaskHistoryView extends TaskHistory
{
	public array $tags      = [];
	public array $observers = [];
}
