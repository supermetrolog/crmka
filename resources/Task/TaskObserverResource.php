<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\TaskObserver;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class TaskObserverResource extends JsonResource
{
	private TaskObserver $resource;

	public function __construct(TaskObserver $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'task_id'       => $this->resource->task_id,
			'user_id'       => $this->resource->user_id,
			'user'          => UserShortResource::make($this->resource->user)->toArray(),
			'viewed_at'     => $this->resource->viewed_at,
			'created_at'    => $this->resource->created_at,
			'updated_at'    => $this->resource->updated_at,
			'created_by_id' => $this->resource->created_by_id
		];
	}
}