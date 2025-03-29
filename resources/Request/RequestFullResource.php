<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\helpers\ArrayHelper;
use app\kernel\web\http\resources\JsonResource;
use app\models\Request;

class RequestFullResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return ArrayHelper::merge(
			RequestResource::make($this->resource)->toArray(),
			[
				'regions'       => RequestRegionResource::collection($this->resource->regions),
				'directions'    => $this->resource->directions,
				'districts'     => $this->resource->districts,
				'objectTypes'   => $this->resource->objectTypes,
				'objectClasses' => $this->resource->objectClasses
			]
		);
	}
}