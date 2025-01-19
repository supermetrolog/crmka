<?php

namespace app\resources\TaskFavorite;

use app\kernel\web\http\resources\JsonResource;
use app\models\TaskFavorite;
use app\resources\Task\TaskResource;

class TaskFavoriteResource extends JsonResource
{
	private TaskFavorite $resource;

	public function __construct(TaskFavorite $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'task'       => TaskResource::tryMakeArray($this->resource->task),
			'created_at' => $this->resource->created_at,
			'deleted_at' => $this->resource->deleted_at
		];
	}
}