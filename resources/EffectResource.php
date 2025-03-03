<?php

declare(strict_types=1);

namespace app\resources;

use app\kernel\web\http\resources\JsonResource;
use app\models\Effect;

class EffectResource extends JsonResource
{
	private Effect $resource;

	public function __construct(Effect $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'          => $this->resource->id,
			'title'       => $this->resource->title,
			'kind'        => $this->resource->kind,
			'description' => $this->resource->description,
			'active'      => $this->resource->active
		];
	}
}
