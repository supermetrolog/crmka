<?php

namespace app\resources\Contact\WayOfInforming;

use app\kernel\web\http\resources\JsonResource;
use app\models\miniModels\WayOfInforming;

class ContactWayOfInformingResource extends JsonResource
{
	private WayOfInforming $resource;

	public function __construct(WayOfInforming $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'  => $this->resource->id,
			'way' => $this->resource->way,
		];
	}
}