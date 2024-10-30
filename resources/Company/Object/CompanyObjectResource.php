<?php

declare(strict_types=1);

namespace app\resources\Company\Object;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;

class CompanyObjectResource extends JsonResource
{
	private Objects $resource;

	public function __construct(Objects $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'               => $this->resource->id,
			'complex_id'       => $this->resource->complex_id,
			'description'      => $this->resource->description,
			'description_auto' => $this->resource->description_auto,
			'last_update'      => $this->resource->last_update,
			'area_building'    => $this->resource->area_building,
			'address'          => $this->resource->address,
			'from_mkad'        => $this->resource->from_mkad,
			'object_class'     => $this->resource->object_class,
			'thumb'            => $this->resource->getThumb(),
			'updated_at'       => $this->resource->getUpdatedAt(),
			'created_at'       => $this->resource->getCreatedAt(),
			'offerMix'         => CompanyObjectOfferMixResource::collection($this->resource->offerMix)
		];
	}
}