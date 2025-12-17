<?php

namespace app\resources\Attribute;

use app\kernel\web\http\resources\JsonResource;
use app\models\AttributeGroup;

class AttributeGroupResource extends JsonResource
{
	private AttributeGroup $resource;

	public function __construct(AttributeGroup $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'name'       => $this->resource->name,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
		];
	}
}