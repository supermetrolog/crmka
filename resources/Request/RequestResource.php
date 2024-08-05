<?php

declare(strict_types=1);

namespace app\resources\Request;

use app\kernel\web\http\resources\JsonResource;
use app\models\Request;
use app\resources\ChatMember\ChatMemberModel\CompanyShortResource;

class RequestResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'                            => $this->resource->id,
			'company_id'                    => $this->resource->company_id,
			'dealType'                      => $this->resource->dealType,
			'expressRequest'                => $this->resource->expressRequest,
			'distanceFromMKAD'              => $this->resource->distanceFromMKAD,
			'distanceFromMKADnotApplicable' => $this->resource->distanceFromMKADnotApplicable,
			'minArea'                       => $this->resource->minArea,
			'maxArea'                       => $this->resource->maxArea,
			'minCeilingHeight'              => $this->resource->minCeilingHeight,
			'maxCeilingHeight'              => $this->resource->maxCeilingHeight,
			'firstFloorOnly'                => $this->resource->firstFloorOnly,
			'heated'                        => $this->resource->heated,
			'trainLine'                     => $this->resource->trainLine,
			'trainLineLength'               => $this->resource->trainLineLength,
			'consultant_id'                 => $this->resource->consultant_id,
			'description'                   => $this->resource->description,
			'pricePerFloor'                 => $this->resource->pricePerFloor,
			'electricity'                   => $this->resource->electricity,
			'haveCranes'                    => $this->resource->haveCranes,
			'status'                        => $this->resource->status,
			'created_at'                    => $this->resource->created_at,
			'updated_at'                    => $this->resource->updated_at,
			'movingDate'                    => $this->resource->movingDate,
			'unknownMovingDate'             => $this->resource->unknownMovingDate,
			'antiDustOnly'                  => $this->resource->antiDustOnly,
			'passive_why'                   => $this->resource->passive_why,
			'passive_why_comment'           => $this->resource->passive_why_comment,
			'water'                         => $this->resource->water,
			'gaz'                           => $this->resource->gaz,
			'steam'                         => $this->resource->steam,
			'shelving'                      => $this->resource->shelving,
			'sewerage'                      => $this->resource->sewerage,
			'name'                          => $this->resource->name,
			'outside_mkad'                  => $this->resource->outside_mkad,
			'region_neardy'                 => $this->resource->region_neardy,
			'contact_id'                    => $this->resource->contact_id,
			'related_updated_at'            => $this->resource->related_updated_at,
			'regions'                       => $this->resource->regions,
			'directions'                    => $this->resource->directions,
			'districts'                     => $this->resource->districts,
			'objectTypes'                   => $this->resource->objectTypes,
			'objectClasses'                 => $this->resource->objectClasses,
			'company'                       => CompanyShortResource::tryMakeArray($this->resource->company),
		];
	}
}