<?php

namespace app\events\Task;

use app\events\AbstractEvent;
use app\models\TaskComment;

class TaskCommentCreatedEvent extends AbstractEvent
{
	private TaskComment $taskComment;

	public function __construct(TaskComment $taskComment)
	{
		$this->taskComment = $taskComment;

		parent::__construct();
	}

	public function getTaskComment(): TaskComment
	{
		return $this->taskComment;
	}
}
