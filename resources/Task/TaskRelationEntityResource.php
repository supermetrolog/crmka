<?php

declare(strict_types=1);

namespace app\resources\Task;

use app\kernel\web\http\resources\JsonResource;
use app\models\TaskRelationEntity;

class TaskRelationEntityResource extends JsonResource
{
	private TaskRelationEntity $resource;

	public function __construct(TaskRelationEntity $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'entity_id'     => $this->resource->entity_id,
			'entity_type'   => $this->resource->entity_type,
			'comment'       => $this->resource->comment,
			'relation_type' => $this->resource->relation_type,
			'created_by_id' => $this->resource->created_by_id,
			'deleted_by_id' => $this->resource->deleted_by_id,
			'created_at'    => $this->resource->created_at,
			'updated_at'    => $this->resource->updated_at,
			'deleted_at'    => $this->resource->deleted_at,
		];
	}
}