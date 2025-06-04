<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\kernel\web\http\resources\JsonResource;
use app\models\OfferMix;
use app\resources\Company\CompanyShortResource;

class TaskRelationEntityOfferMixResource extends JsonResource
{
	private OfferMix $resource;

	public function __construct(OfferMix $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                    => $this->resource->id,
			'visual_id'             => $this->resource->visual_id,
			'address'               => $this->resource->address,
			'area_min'              => $this->resource->area_min,
			'area_max'              => $this->resource->area_max,
			'class'                 => $this->resource->class,
			'is_land'               => $this->resource->is_land,
			'is_fake'               => $this->resource->is_fake,
			'test_only'             => $this->resource->test_only,
			'company_id'            => $this->resource->company_id,
			'company'               => CompanyShortResource::tryMakeArray($this->resource->company),
			'complex_id'            => $this->resource->complex_id,
			'deal_type'             => $this->resource->deal_type,
			'deal_type_name'        => $this->resource->deal_type_name,
			'last_update'           => $this->resource->last_update,
			'object_id'             => $this->resource->object_id,
			'price_sale_min'        => $this->resource->price_sale_min,
			'price_sale_max'        => $this->resource->price_sale_max,
			'price_floor_min'       => $this->resource->price_floor_min,
			'price_floor_max'       => $this->resource->price_floor_max,
			'price_safe_pallet_min' => $this->resource->price_safe_pallet_min,
			'price_safe_pallet_max' => $this->resource->price_safe_pallet_max,
			'status'                => $this->resource->status,
			'thumb'                 => $this->resource->getThumb(),
			'type_id'               => $this->resource->type_id
		];
	}
}