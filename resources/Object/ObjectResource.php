<?php

declare(strict_types=1);

namespace app\resources\Object;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;
use app\models\Request;

class ObjectResource extends JsonResource
{
	private Objects $resource;

	public function __construct(Objects $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return $this->resource->toArray();
	}
}