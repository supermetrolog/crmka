<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\TaskComment;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;

class TaskCommentResource extends JsonResource
{
	private TaskComment $resource;

	public function __construct(TaskComment $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'message'       => $this->resource->message,
			'created_at'    => $this->resource->created_at,
			'updated_at'    => $this->resource->updated_at,
			'deleted_at'    => $this->resource->deleted_at,
			'created_by_id' => $this->resource->created_by_id,
			'created_by'    => UserShortResource::tryMake($this->resource->createdBy)->toArray()
		];
	}
}