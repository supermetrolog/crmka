<?php

namespace app\events\Task;

use app\events\AbstractEvent;
use app\models\Task;
use app\models\User;

abstract class TaskAbstractEvent extends AbstractEvent
{
	public Task $task;
	public User $initiator;

	public function __construct(Task $task, User $initiator)
	{
		$this->task      = $task;
		$this->initiator = $initiator;

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
}
