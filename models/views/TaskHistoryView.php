<?php

namespace app\models\views;

use app\models\Media;
use app\models\TaskEvent;
use app\models\TaskHistory;
use app\models\TaskTag;
use app\models\User\User;

/**
 * @property TaskEvent[] $events
 */
class TaskHistoryView extends TaskHistory
{
	/** @var TaskTag[] */
	public array $tags = [];

	/** @var User[] */
	public array $observers = [];

	/** @var Media[] */
	public array $files = [];
}
