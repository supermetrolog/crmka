<?php

declare(strict_types=1);

namespace app\resources\Folder;

use app\kernel\web\http\resources\JsonResource;
use app\models\Folder;

class FolderResource extends JsonResource
{
	private Folder $resource;

	public function __construct(Folder $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'user_id'    => $this->resource->user_id,
			'name'       => $this->resource->name,
			'color'      => $this->resource->color,
			'icon'       => $this->resource->icon,
			'category'   => $this->resource->category,
			'sort_order' => $this->resource->sort_order,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'deleted_at' => $this->resource->deleted_at
		];
	}
}