<?php

namespace app\resources\AttributeOption;

use app\kernel\web\http\resources\JsonResource;
use app\models\AttributeOption;
use app\resources\Attribute\AttributeResource;

class AttributeOptionResource extends JsonResource
{
	private AttributeOption $resource;

	public function __construct(AttributeOption $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'attribute'  => AttributeResource::tryMakeArray($this->resource->attributeRel),
			'value'      => $this->resource->value,
			'label'      => $this->resource->label,
			'sort_order' => $this->resource->sort_order,
			'created_at' => $this->resource->created_at,
		];
	}
}