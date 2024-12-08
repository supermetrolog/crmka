<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\User;
use app\models\views\TaskHistoryView;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use UnexpectedValueException;

class TaskHistoryResource extends JsonResource
{
	private TaskHistoryView $resource;

	public function __construct(TaskHistoryView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'       => $this->resource->id,
			'snapshot' => [
				'user_id'         => $this->resource->user_id,
				'message'         => $this->resource->message,
				'status'          => $this->resource->status,
				'start'           => $this->resource->start,
				'end'             => $this->resource->end,
				'created_by_type' => $this->resource->created_by_type,
				'created_by_id'   => $this->resource->created_by_id,
				'created_at'      => $this->resource->created_at,
				'impossible_to'   => $this->resource->impossible_to,
				'user'            => UserShortResource::make($this->resource->user)->toArray(),
				'created_by'      => $this->getCreatedBy(),
				'tags'            => TaskTagResource::collection($this->resource->tags),
				'observers'       => UserShortResource::collection($this->resource->observers),
			],
			'prev_id'  => $this->resource->prev_id,
			'task_id'  => $this->resource->task_id,
			'events'   => $this->resource->events
		];
	}

	private function getCreatedBy(): JsonResource
	{
		$createdBy = $this->resource->createdBy;

		if ($createdBy instanceof User) {
			return new UserShortResource($createdBy);
		}

		throw new UnexpectedValueException('Unknown created by type');
	}
}