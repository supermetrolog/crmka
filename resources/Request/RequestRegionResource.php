<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\RequestRegion;
use app\resources\Old\Location\OldLocationRegionResource;

class RequestRegionResource extends JsonResource
{
	private RequestRegion $resource;

	public function __construct(RequestRegion $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'     => $this->resource->id,
			'region' => $this->resource->region,
			'info'   => OldLocationRegionResource::tryMakeArray($this->resource->info)
		];
	}
}