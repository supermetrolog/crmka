<?php

declare(strict_types=1);

namespace app\resources\Folder;

use app\kernel\web\http\resources\JsonResource;
use app\models\views\FolderSearchView;

class FolderSearchResource extends JsonResource
{
	private FolderSearchView $resource;

	public function __construct(FolderSearchView $resource)
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
			'morph'      => $this->resource->morph,
			'sort_order' => $this->resource->sort_order,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'deleted_at' => $this->resource->deleted_at,

			'entities_count' => $this->resource->entities_count
		];
	}
}