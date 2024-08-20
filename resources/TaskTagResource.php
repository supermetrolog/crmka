<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\TaskTag;

class TaskTagResource extends JsonResource
{
	private TaskTag $resource;

	public function __construct(TaskTag $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'name'        => $this->resource->name,
			'description' => $this->resource->description,
			'color'       => $this->resource->color,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
			'deleted_at'  => $this->resource->deleted_at
		];
	}
}