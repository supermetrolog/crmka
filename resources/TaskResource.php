<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Task;
use app\models\User;
use app\resources\User\UserResource;
use UnexpectedValueException;

class TaskResource extends JsonResource
{
	private Task $task;

	public function __construct(Task $task)
	{
		$this->task = $task;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->task->id,
			'user_id'         => $this->task->user_id,
			'message'         => $this->task->message,
			'status'          => $this->task->status,
			'start'           => $this->task->start,
			'end'             => $this->task->end,
			'created_by_type' => $this->task->created_by_type,
			'created_by_id'   => $this->task->created_by_id,
			'created_at'      => $this->task->created_at,
			'updated_at'      => $this->task->updated_at,
			'deleted_at'      => $this->task->deleted_at,
			'created_by'      => $this->getCreatedBy()->toArray()
		];
	}

	private function getCreatedBy(): JsonResource
	{
		$createdBy = $this->task->createdBy;

		if ($createdBy instanceof User) {
			return new UserResource($createdBy);
		}

		throw new UnexpectedValueException('Unknown created by type');
	}
}