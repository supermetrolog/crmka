<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Field;

class FieldResource extends JsonResource
{
	private Field $resource;

	public function __construct(Field $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'field_type' => $this->resource->field_type,
			'type'       => $this->resource->type,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'deleted_at' => $this->resource->deleted_at,
		];
	}
}