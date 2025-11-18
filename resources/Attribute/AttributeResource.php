<?php

namespace app\resources\Attribute;

use app\kernel\web\http\resources\JsonResource;
use app\models\Attribute;

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
			'id'          => $this->resource->id,
			'kind'        => $this->resource->kind,
			'label'       => $this->resource->label,
			'description' => $this->resource->description,
			'value_type'  => $this->resource->value_type,
			'input_type'  => $this->resource->input_type,
			'created_at'  => $this->resource->created_at
		];
	}
}