<?php

declare(strict_types=1);

namespace app\resources\Media;

use app\kernel\web\http\resources\JsonResource;
use app\models\Media;

class MediaShortResource extends JsonResource
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
			'original_name' => $this->resource->original_name,
			'extension'     => $this->resource->extension,
			'path'          => $this->resource->path,
			'category'      => $this->resource->category,
			'created_at'    => $this->resource->created_at,
			'deleted_at'    => $this->resource->deleted_at,
			'mime_type'     => $this->resource->mime_type,
			'src'           => $this->resource->src
		];
	}
}
