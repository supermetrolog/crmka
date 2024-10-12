<?php

declare(strict_types=1);

namespace app\resources\Call;

use app\kernel\web\http\resources\JsonResource;
use app\models\Call;

class CallShortResource extends JsonResource
{
	private Call $resource;

	public function __construct(Call $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'         => $this->resource->id,
			'user_id'    => $this->resource->user_id,
			'created_at' => $this->resource->created_at,
			'updated_at' => $this->resource->updated_at,
			'deleted_at' => $this->resource->deleted_at
		];
	}
}