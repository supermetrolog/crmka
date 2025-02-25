<?php

declare(strict_types=1);

namespace app\resources\Media;

use app\kernel\web\http\resources\JsonResource;
use app\models\Media;

class MediaResource extends JsonResource
{
	private Media $resource;

	public function __construct(Media $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'            => $this->resource->id,
			'name'          => $this->resource->name,
			'original_name' => $this->resource->original_name,
			'extension'     => $this->resource->extension,
			'path'          => $this->resource->path,
			'category'      => $this->resource->category,
			'created_at'    => $this->resource->created_at,
			'deleted_at'    => $this->resource->deleted_at,
			'model_type'    => $this->resource->model_type,
			'model_id'      => $this->resource->model_id,
			'mime_type'     => $this->resource->mime_type,
			'src'           => $this->resource->getSrc()
		];
	}
}
