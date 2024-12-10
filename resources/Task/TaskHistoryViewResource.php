<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\TaskHistoryView;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class TaskHistoryViewResource extends JsonResource
{
	private TaskHistoryView $resource;

	public function __construct(TaskHistoryView $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'snapshot'   => [
				'message'       => $this->resource->message,
				'status'        => $this->resource->status,
				'start'         => $this->resource->start,
				'end'           => $this->resource->end,
				'created_at'    => $this->resource->created_at,
				'impossible_to' => $this->resource->impossible_to,
				'user_id'       => $this->resource->user_id,
				'user'          => UserShortResource::make($this->resource->user)->toArray(),
				'tags'          => TaskTagResource::collection($this->resource->tags),
				'observers'     => UserShortResource::collection($this->resource->observers),
			],
			'prev_id'    => $this->resource->prev_id,
			'task_id'    => $this->resource->task_id,
			'events'     => $this->resource->events,
			'created_by' => UserShortResource::tryMakeArray($this->resource->createdBy)
		];
	}
}