<?php

namespace app\resources\Attribute;

use app\kernel\web\http\resources\JsonResource;
use app\models\AttributeRule;

class AttributeRuleResource extends JsonResource
{
	private AttributeRule $resource;

	public function __construct(AttributeRule $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                 => $this->resource->id,
			'attribute_id'       => AttributeResource::tryMakeArray($this->resource->attributeRel),
			'attribute_group_id' => AttributeGroupResource::tryMakeArray($this->resource->attributeGroup),
			'entity_type'        => $this->resource->entity_type,
			'is_required'        => $this->resource->is_required,
			'is_inheritable'     => $this->resource->is_inheritable,
			'is_editable'        => $this->resource->is_editable,
			'status'             => $this->resource->status,
			'sort_order'         => $this->resource->sort_order,
			'created_at'         => $this->resource->created_at,
			'updated_at'         => $this->resource->updated_at,
		];
	}
}