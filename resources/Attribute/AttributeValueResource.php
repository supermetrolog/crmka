<?php

namespace app\resources\Attribute;

use app\kernel\web\http\resources\JsonResource;
use app\models\AttributeValue;

class AttributeValueResource extends JsonResource
{
	private AttributeValue $resource;

	public function __construct(AttributeValue $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'attribute'   => AttributeResource::tryMakeArray($this->resource->attributeRel),
			'entity_type' => $this->resource->entity_type,
			'entity_id'   => $this->resource->entity_id,
			'value'       => $this->resource->value,
			'created_at'  => $this->resource->created_at,
			'updated_at'  => $this->resource->updated_at,
		];
	}
}