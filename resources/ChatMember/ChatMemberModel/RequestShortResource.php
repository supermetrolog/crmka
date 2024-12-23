<?php

declare(strict_types=1);

namespace app\resources\ChatMember\ChatMemberModel;

use app\kernel\web\http\resources\JsonResource;
use app\models\Request;

class RequestShortResource extends JsonResource
{
	private Request $resource;

	public function __construct(Request $resource)
	{
		$this->resource = $resource;
	}

	public function toArray(): array
	{
		return [
			'id'               => $this->resource->id,
			'status'           => $this->resource->status,
			'expressRequest'   => $this->resource->expressRequest,
			'company_id'       => $this->resource->company_id,
			'dealType'         => $this->resource->dealType,
			'minArea'          => $this->resource->minArea,
			'maxArea'          => $this->resource->maxArea,
			'distanceFromMKAD' => $this->resource->distanceFromMKAD,
			'regions'          => $this->resource->regions,
			'directions'       => $this->resource->directions,
			'districts'        => $this->resource->districts,
			'objectTypes'      => $this->resource->objectTypes,
			'objectClasses'    => $this->resource->objectClasses,
			'consultant_id'    => $this->resource->consultant_id,
			'consultant'       => UserShortResource::tryMakeArray($this->resource->consultant),
			'created_at'       => $this->resource->created_at,
			'updated_at'       => $this->resource->updated_at,
			'company'          => CompanyShortResource::tryMakeArray($this->resource->company),
		];
	}
}