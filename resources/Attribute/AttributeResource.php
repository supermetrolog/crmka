<?php

namespace app\resources\Attribute;

use app\kernel\web\http\resources\JsonResource;
use app\models\Attribute;
use app\resources\User\UserResource;

class AttributeResource extends JsonResource
{
	private Attribute $resource;

	public function __construct(Attribute $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'kind'          => $this->resource->kind,
			'label'         => $this->resource->label,
			'description'   => $this->resource->description,
			'value_type'    => $this->resource->value_type,
			'input_type'    => $this->resource->input_type,
			'created_by_id' => UserResource::tryMakeArray($this->resource->created_by),
			'created_at'    => $this->resource->created_at,
			'deleted_at'    => $this->resource->deleted_at,
		];
	}
}