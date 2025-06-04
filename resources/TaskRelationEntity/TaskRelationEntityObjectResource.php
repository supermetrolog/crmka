<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\kernel\web\http\resources\JsonResource;
use app\models\Objects;
use app\resources\Company\CompanyShortResource;

class TaskRelationEntityObjectResource extends JsonResource
{
	private Objects $resource;

	public function __construct(Objects $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'              => $this->resource->id,
			'address'         => $this->resource->address,
			'complex_id'      => $this->resource->complex_id,
			'contact_id'      => $this->resource->contact_id,
			'is_land'         => $this->resource->is_land,
			'test_only'       => $this->resource->test_only,
			'object_class'    => $this->resource->object_class,
			'area_field_full' => $this->resource->area_field_full,
			'area_floor_full' => $this->resource->area_floor_full,
			'area_building'   => $this->resource->area_building,
			'floors_count'    => $this->resource->getFloorsCount(),
			'area_outside'    => $this->resource->area_outside,
			'thumb'           => $this->resource->getThumb(),
			'photos'          => $this->resource->getPhotos(),
			'updated_at'      => $this->getUpdatedAt(),
			'created_at'      => $this->resource->publ_time,
			'company_id'      => $this->resource->company_id,
			'company'         => CompanyShortResource::tryMakeArray($this->resource->company)
		];
	}

	public function getUpdatedAt(): int
	{
		if ($this->resource->last_update > 0) {
			return $this->resource->last_update;
		}

		return $this->resource->publ_time;
	}
}