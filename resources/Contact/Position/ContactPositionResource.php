<?php

declare(strict_types=1);

namespace app\resources\Contact\Position;

use app\kernel\web\http\resources\JsonResource;
use app\models\ContactPosition;

class ContactPositionResource extends JsonResource
{
	private ContactPosition $resource;

	public function __construct(ContactPosition $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'name'          => $this->resource->name,
			'slug'          => $this->resource->slug,
			'short_name'    => $this->resource->short_name,
			'description'   => $this->resource->description,
			'color'         => $this->resource->color,
			'icon'          => $this->resource->icon,
			'is_active'     => $this->resource->is_active,
			'sort_order'    => $this->resource->sort_order,
			'created_by_id' => $this->resource->created_by_id,
			'created_at'    => $this->resource->created_at,
			'updated_at'    => $this->resource->updated_at,
			'deleted_at'    => $this->resource->deleted_at
		];
	}
}