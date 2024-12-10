<?php

namespace app\events\Task;

use app\events\AbstractEvent;
use app\models\Task;
use app\models\User;

class UpdateTaskEvent extends AbstractEvent
{
	public Task $task;
	public User $initiator;

	/** @var string[] */
	public array $eventTypes;

	/** @param string[] $eventTypes */
	public function __construct(Task $task, User $initiator, array $eventTypes = [])
	{
		$this->task       = $task;
		$this->initiator  = $initiator;
		$this->eventTypes = $eventTypes;

		parent::__construct();
	}

	public function getTask(): Task
	{
		return $this->task;
	}

	public function getInitiator(): User
	{
		return $this->initiator;
	}

	public function getEventTypes(): array
	{
		return $this->eventTypes;
	}
}
