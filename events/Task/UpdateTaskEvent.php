<?php

namespace app\events\Task;

use app\models\Task;
use app\models\User\User;

class UpdateTaskEvent extends AbstractTaskEvent
{
	/** @var string[] */
	public array $eventTypes;

	/** @param string[] $eventTypes */
	public function __construct(Task $task, User $initiator, array $eventTypes = [])
	{
		$this->eventTypes = $eventTypes;

		parent::__construct($task, $initiator);
	}

	public function getEventTypes(): array
	{
		return $this->eventTypes;
	}
}
