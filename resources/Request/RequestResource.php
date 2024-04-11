<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\Request;

class RequestResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return $this->resource->toArray();
	}
}