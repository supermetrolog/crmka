<?php

declare(strict_types=1);

namespace app\resources\TaskRelationEntity;

use app\kernel\web\http\resources\JsonResource;
use app\models\Request;
use app\resources\ChatMember\ChatMemberModel\UserShortResource;
use app\resources\Company\CompanyShortResource;
use app\resources\Request\RequestRegionResource;

class TaskRelationEntityRequestResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                  => $this->resource->id,
			'company_id'          => $this->resource->company_id,
			'dealType'            => $this->resource->dealType,
			'expressRequest'      => $this->resource->expressRequest,
			'distanceFromMKAD'    => $this->resource->distanceFromMKAD,
			'minArea'             => $this->resource->minArea,
			'maxArea'             => $this->resource->maxArea,
			'consultant_id'       => $this->resource->consultant_id,
			'description'         => $this->resource->description,
			'pricePerFloor'       => $this->resource->pricePerFloor,
			'status'              => $this->resource->status,
			'created_at'          => $this->resource->created_at,
			'passive_why'         => $this->resource->passive_why,
			'passive_why_comment' => $this->resource->passive_why_comment,
			'name'                => $this->resource->name,
			'outside_mkad'        => $this->resource->outside_mkad,
			'region_neardy'       => $this->resource->region_neardy,
			'directions'          => $this->resource->directions,
			'districts'           => $this->resource->districts,

			'format_name'           => $this->resource->getFormatName(),
			'price_per_floor_month' => $this->resource->getPricePerFloorMonth(),

			'regions'    => RequestRegionResource::collection($this->resource->regions),
			'consultant' => UserShortResource::tryMakeArray($this->resource->consultant),
			'company'    => CompanyShortResource::tryMakeArray($this->resource->company)
		];
	}
}